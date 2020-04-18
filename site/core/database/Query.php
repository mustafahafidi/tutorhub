<?php

namespace Database;


class Query
{
    /**
     * @var Database
     */
    private $db;
    /**
     * @var callable
     */
    private $hydrator;
    private $stack = [];
    private $idx = -1;

    /**
     * Query constructor.
     *
     * @param Database $db       Target database.
     * @param callable $hydrator Hydrator that accepts (Database, query results).
     */
    public function __construct(Database $db, callable $hydrator)
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->pushFrame();
    }

    /**
     * Adds a SELECT clause to the current query.
     *
     * @param string   $table     Table to select from.
     * @param string[] ...$fields Fields to select.
     * @return self $this.
     */
    public function select(string $table, string ...$fields) : self
    {
        $fields_str = empty($fields) ? '*' : join(',',
            array_map(function ($x) { return "`$x`"; }, $fields));
        $this->stack[$this->idx]['query'] .= "SELECT $fields_str FROM $table ";
        return $this;
    }

    /**
     * Adds a WHERE clause to the current query, or an
     * AND clause to the current WHERE clause or group.
     *
     * @param string $field Field to compare.
     * @param mixed  $value Value to compare to.
     * @param string $op    Comparison operator.
     * @return self $this.
     */
    public function where(string $field, $value, string $op='=') : self
    {
        $comp = "$field $op ?";
        if (!$this->stack[$this->idx]['gotWhere']) {
            $this->stack[$this->idx]['query'] .= "WHERE $comp ";
            $this->stack[$this->idx]['gotWhere'] = true;
            $this->stack[$this->idx]['gotFirstCondition'] = true;
        } else if (!$this->stack[$this->idx]['gotFirstCondition']) {
            $this->stack[$this->idx]['query'] .= "$comp ";
            $this->stack[$this->idx]['gotFirstCondition'] = true;
        } else {
            $this->stack[$this->idx]['query'] .= "AND $comp ";
        }
        $this->stack[$this->idx]['params'][] = $value;
        return $this;
    }

    /**
     * Adds an OR clause to the current WHERE clause or group.
     *
     * @param string $field Field to compare.
     * @param mixed  $value Value to compare to.
     * @param string $op    Comparison operator.
     * @return self $this.
     */
    public function orWhere(string $field, $value, string $op='=') : self
    {
        $comp = "$field $op ?";
        if (!$this->stack[$this->idx]['gotFirstCondition']) {
            $this->stack[$this->idx]['query'] .= "$comp ";
            $this->stack[$this->idx]['gotFirstCondition'] = true;
        } else {
            $this->stack[$this->idx]['query'] .= "OR $comp ";
        }
        $this->stack[$this->idx]['params'][] = $value;
        return $this;
    }

    /**
     * Begins a WHERE group to be added to the current query, or a
     * condition group to be ANDed to the current WHERE clause or group.
     *
     * @return self $this.
     */
    public function whereGroup() : self
    {
        if (!$this->stack[$this->idx]['gotWhere']) {
            $this->stack[$this->idx]['query'] .= 'WHERE (';
            $this->stack[$this->idx]['gotWhere'] = true;
        } else if (!$this->stack[$this->idx]['gotFirstCondition']) {
            $this->stack[$this->idx]['query'] .= '(';
            $this->stack[$this->idx]['gotFirstCondition'] = true;
        } else {
            $this->stack[$this->idx]['query'] .= 'AND (';
        }
        $this->pushFrame();
        $this->stack[$this->idx]['gotWhere'] = true;
        return $this;
    }

    /**
     * Begins a condition group to be ORed to the current WHERE clause or group.
     *
     * @return self $this.
     */
    public function orWhereGroup() : self
    {
        if (!$this->stack[$this->idx]['gotFirstCondition']) {
            $this->stack[$this->idx]['query'] .= '(';
            $this->stack[$this->idx]['gotFirstCondition'] = true;
        } else {
            $this->stack[$this->idx]['query'] .= 'OR (';
        }
        $this->pushFrame();
        $this->stack[$this->idx]['gotWhere'] = true;
        return $this;
    }

    /**
     * Ends a group.
     *
     * @return self $this.
     */
    public function endGroup() : self
    {
        $this->stack[$this->idx]['query'] = trim($this->stack[$this->idx]['query']) . ') ';
        $this->popFrame();
        $this->stack[$this->idx]['gotFirstCondition'] = true;
        return $this;
    }

    /**
     * Adds a LIMIT clause to the current query.
     *
     * @param int $num Limit parameter.
     * @return self $this.
     */
    public function limit(int $num) : self
    {
        $this->stack[$this->idx]['query'] .= "LIMIT $num ";
        return $this;
    }

    /**
     * Gets this query's prepared statement SQL.
     */
    public function getSql() : string
    {
        return trim($this->stack[0]['query']);
    }

    /**
     * Gets this query's prepared statement parameters.
     */
    public function getParams() : array
    {
        return $this->stack[0]['params'];
    }

    /**
     * Performs this query.
     *
     * @return mixed The hydrator's return value.
     */
    public function get()
    {
        $results = $this->db->query($this);
        return call_user_func($this->hydrator, $results);
    }

    private function pushFrame()
    {
        $this->stack[] = [
            'query' => '',
            'params' => [],
            'gotWhere' => false,
            'gotFirstCondition' => false
        ];
        $this->idx++;
    }

    private function popFrame()
    {
        $frame = array_pop($this->stack);
        $this->idx--;
        $this->stack[$this->idx]['query'] .= $frame['query'];
        $this->stack[$this->idx]['params'] = array_merge($this->stack[$this->idx]['params'], $frame['params']);
    }
}

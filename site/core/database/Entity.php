<?php

namespace Database;


class Entity implements \ArrayAccess
{
    /**
     * @var string Database table for this entity.
     *             Subclasses must initialize this property.
     */
    protected static $dbTable;
    /**
     * @var string Database column name for the primary key.
     *             Subclasses must initialize this property.
     */
    protected static $dbKey;
    /**
     * @var array Object array key => attribute name map.
     *            Subclasses must initialize this property.
     */
    protected static $arrayMap = [];
    /**
     * @var string Database primary key for this entity.
     */
    protected $key = null;
    /**
     * @var array Database attributes for this entity.
     */
    protected $attributes = [];
    /**
     * @var Database
     */
    private $db;

    /**
     * Entity constructor.
     *
     * @param Database $db Target database.
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @return mixed|null This entity's database primary key.
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->attributes[static::$arrayMap[$offset]]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function &offsetGet($offset)
    {
        if (!isset(static::$arrayMap[$offset]))
            throw new \OutOfRangeException("Invalid key: $offset");
        return $this->attributes[static::$arrayMap[$offset]] ?? null;
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        if (!isset(static::$arrayMap[$offset]))
            throw new \OutOfRangeException("Invalid key: $offset");
        $this->attributes[static::$arrayMap[$offset]] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[static::$arrayMap[$offset]]);
    }

    /**
     * Inserts or updates this entity in the database.
     *
     * @return bool True if changes were made, false otherwise.
     */
    public function commit() : bool
    {
        $params = array_merge(...array_map(
            function ($k, $v) { return [':' . $k => $v]; },
            array_keys($this->attributes), $this->attributes));

        if ($this->key !== null) {
            $params[':' . static::$dbKey] = $this->key;
            $sql = 'UPDATE `' . static::$dbTable. '` SET '
                . join(',', array_map(
                    function ($k) { return "`$k`=:$k"; },
                    array_keys($this->attributes)))
                . ' WHERE `' . static::$dbKey . '`=:' . static::$dbKey;
        } else {
            $sql = 'INSERT INTO `' . static::$dbTable . '` ('
                . join(',', array_map(
                    function ($x) { return "`$x`"; },
                    array_keys($this->attributes)))
                . ') VALUES ('
                . join(',', array_keys($params))
                . ')';
        }

        $ret = $this->db->execute($sql, $params) > 0;
        if ($ret && $this->key === null)
            $this->key = $this->db->lastInsertId();
        return $ret;
    }

    /**
     * Creates a selection query for this entity.
     *
     * @param Database $db Target database.
     * @return Query
     */
    public static function query(Database $db) : Query
    {
        $query = new Query($db, function($results) use ($db) {
            return self::hydrate($db, $results);
        });
        return $query->select(static::$dbTable);
    }

    /**
     * Retrieves all instances of this entity matching the given keys
     * from the database.
     *
     * @param Database $db   Target database.
     * @param array    $keys Set of keys.
     * @return array Found entities, as a key => entity array.
     */
    public static function fromKeys(Database $db, array $keys) : array
    {
        if (empty($keys))
            return [];

        $query = static::query($db);
        $first = true;
        foreach ($keys as $key) {
            if ($first)
                $query->where(static::$dbKey, $key);
            else
                $query->orWhere(static::$dbKey, $key);
            $first = false;
        }

        return $query->get();
    }

    /**
     * Retrieves an entity from its database key.
     *
     * @param Database $db  Target database.
     * @param mixed    $key Value of key.
     * @return self|null The constructed entity, or null if not found.
     */
    public static function fromKey(Database $db, $key)
    {
        $objs = static::fromKeys($db, [$key]);
        return empty($objs) ? null : reset($objs);
    }

    /**
     * Retrieves all instances of this entity that match the given
     * attributes from the database.
     *
     * @param Database $db      Target database.
     * @param array $attributes Attributes to match.
     *                          First dimension is ORed, second is ANDed.
     * @return array Found entities, as a key => entity array.
     */
    public static function where(Database $db, array $attributes) : array
    {
        if (empty($attributes))
            return [];

        $query = static::query($db);
        $first = true;
        foreach ($attributes as $cond) {
            if ($first)
                $query->whereGroup();
            else
                $query->orWhereGroup();
            foreach ($cond as $k => $v)
                $query->where(static::$arrayMap[$k], $v);
            $query->endGroup();
            $first = false;
        }

        return $query->get();
    }

    private static function hydrate(Database $db, array $results) : array
    {
        $entities = [];
        foreach ($results as $result) {
            $entity = new static($db);
            $entity->key = $result[static::$dbKey];
            unset($result[static::$dbKey]);
            $entity->attributes = $result;
            $entities[$entity->key] = $entity;
        }
        return $entities;
    }
}

<?php

namespace Database;


class Database
{
    /**
     * @var \PDO
     */
    private $db;

    /**
     * Database constructor.
     * Connects to the database.
     *
     * @param array $config Database configuration.
     */
    public function __construct(array $config)
    {
        $this->db = new \PDO("mysql:host=${config['db_host']};port=${config['db_port']};dbname=${config['db_name']}",
            $config['db_user'], $config['db_pass']);
        $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Executes a selection query.
     *
     * @param Query $query Query to execute.
     * @return array Query results.
     */
    public function query(Query $query) : array
    {
        $stmt = $this->db->prepare($query->getSql());
        $stmt->execute($query->getParams());
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Executes an insert, update or delete statement.
     *
     * @param string $sql SQL statement.
     * @param array $params Prepared statement parameters.
     * @return int Number of affected rows.
     */
    public function execute(string $sql, array $params) : int
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Gets the last insertion ID.
     */
    public function lastInsertId() : string
    {
        return $this->db->lastInsertId();
    }
}

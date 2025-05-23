<?php

namespace Core\Database;

use PDO;

/**
 * Class QueryLogger
 * @package Core\Database
 *
 * This class is for debugging purpose only
 */
class QueryLogger{
    protected $pdo;
    protected $queries = [];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function log($query, $bindings)
    {
        $this->queries[] = [
            'query' => $query,
            'bindings' => $bindings,
            'time' => microtime(true)
        ];
    }

    public function getQueries()
    {
        return $this->queries;
    }
}

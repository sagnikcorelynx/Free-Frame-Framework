<?php

namespace Core;

use PDO;
use PDOException;

class Model
{
    protected static $connection;
    protected $table;

    public function __construct()
    {
        if (!self::$connection) {
            $this->connect();
        }
    }

    protected function connect()
    {
        $config = require __DIR__ . '/../config/database.php';

        if ($config['driver'] === 'mysql') {
            $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8";
            try {
                self::$connection = new PDO($dsn, $config['username'], $config['password']);
                self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log($e->getMessage(), 3, __DIR__ . '/../Storage/Logs/error.log');
                die("Database connection error");
            }
        } else {
            $dsn = "mongodb://{$config['host']}:{$config['port']}";
            try {
                self::$connection = new \MongoDB\Driver\Manager($dsn);
            } catch (\MongoDB\Driver\Exception\Exception $e) {
                error_log($e->getMessage(), 3, __DIR__ . '/../Storage/Logs/error.log');
                die("Database connection error");
            }
        }
    }

    public static function getConnection()
    {
        return self::$connection;
    }

    public function all()
    {
        $stmt = self::$connection->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = self::$connection->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
<?php

namespace App\Core;

use PDO;
use MongoDB\Client as MongoClient;

class Database
{
    protected $connection;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config/database.php';
        $default = $config['default'];
        $settings = $config['connections'][$default];

        if ($default === 'mysql') {
            $dsn = "mysql:host={$settings['host']};port={$settings['port']};dbname={$settings['database']}";
            $this->connection = new PDO($dsn, $settings['username'], $settings['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } elseif ($default === 'mongodb') {
            $uri = "mongodb://{$settings['host']}:{$settings['port']}";
            $client = new MongoClient($uri);
            $this->connection = $client->{$settings['database']};
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

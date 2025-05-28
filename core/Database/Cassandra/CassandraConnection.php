<?php
namespace Core\Database\Cassandra;

use Cassandra;

class CassandraConnection
{
    protected static ?Cassandra\Session $session = null;

    public static function connect(): Cassandra\Session
    {
        if (!self::$session) {
            $config = config('database.cassandra');

            $cluster = Cassandra::cluster()
                ->withContactPoints($config['host'])
                ->withPort((int)$config['port']);

            if (!empty($config['username']) && !empty($config['password'])) {
                $cluster = $cluster->withCredentials($config['username'], $config['password']);
            }

            self::$session = $cluster->build()->connect($config['database']);
        }

        return self::$session;
    }
}
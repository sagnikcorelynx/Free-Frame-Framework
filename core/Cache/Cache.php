<?php
namespace Core\Cache;

class Cache
{
    protected static $driver;

    public static function driver(string $driver = null): CacheInterface
    {
        if (self::$driver) return self::$driver;

        $driver = $driver ?? config('cache.driver', 'file');

        switch ($driver) {
            case 'redis':
                self::$driver = new RedisCache();
                break;
            case 'memcached':
                self::$driver = new MemcachedCache();
                break;
            case 'file':
            default:
                self::$driver = new FileCache();
                break;
        }

        return self::$driver;
    }

    public static function get(string $key)
    {
        return self::driver()->get($key);
    }

    public static function put(string $key, $value, int $ttl = 3600)
    {
        return self::driver()->put($key, $value, $ttl);
    }

    public static function remember(string $key, int $ttl, callable $callback)
    {
        return self::driver()->remember($key, $ttl, $callback);
    }

    public static function forget(string $key): bool
    {
        return self::driver()->forget($key);
    }
}
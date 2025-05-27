<?php
namespace Core\Cache;

use Redis;

class RedisCache implements CacheInterface
{
    protected Redis $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function get(string $key)
    {
        $value = $this->redis->get($key);
        return $value ? unserialize($value) : null;
    }

    public function put(string $key, $value, int $ttl = 3600)
    {
        return $this->redis->setex($key, $ttl, serialize($value));
    }

    public function remember(string $key, int $ttl, callable $callback)
    {
        $value = $this->get($key);
        if ($value !== null) return $value;

        $value = $callback();
        $this->put($key, $value, $ttl);
        return $value;
    }

    public function forget(string $key): bool
    {
        return $this->redis->del($key) > 0;
    }
}


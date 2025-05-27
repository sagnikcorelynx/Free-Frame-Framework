<?php
namespace Core\Cache;

use Memcached;

class MemcachedCache implements CacheInterface
{
    /** @var Memcached */
    private $memcached;

    public function __construct()
    {
        $this->memcached = new Memcached();
        $this->memcached->addServer(config('cache.memcached.host'), config('cache.memcached.port'));
    }

    public function get(string $key)
    {
        return $this->memcached->get($key);
    }

    public function put(string $key, $value, int $ttl = 3600)
    {
        $this->memcached->set($key, $value, $ttl);
    }

    public function remember(string $key, int $ttl, callable $callback)
    {
        if ($value = $this->get($key)) {
            return $value;
        }

        $value = $callback();
        $this->put($key, $value, $ttl);
        return $value;
    }

    public function forget(string $key)
    {
        $this->memcached->delete($key);
    }
}

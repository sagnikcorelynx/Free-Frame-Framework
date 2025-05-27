<?php
namespace Core\Cache;

interface CacheInterface
{
    public function get(string $key);
    public function put(string $key, $value, int $ttl = 3600);
    public function remember(string $key, int $ttl, callable $callback);
    public function forget(string $key): bool;
}

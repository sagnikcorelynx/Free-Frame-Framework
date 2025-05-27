<?php
namespace Core\Cache;

class FileCache implements CacheInterface
{
    protected $path;

    public function __construct()
    {
        $this->path = storage_path('Cache');
        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    protected function file(string $key): string
    {
        return $this->path . '/' . md5($key) . '.cache.php';
    }

    public function get(string $key)
    {
        $file = $this->file($key);
        if (!file_exists($file)) return null;

        $data = include $file;

        return ($data['expires_at'] > time()) ? $data['value'] : null;
    }

    public function put(string $key, $value, int $ttl = 3600)
    {
        $file = $this->file($key);
        $data = [
            'value' => $value,
            'expires_at' => time() + $ttl,
        ];
        file_put_contents($file, '<?php return ' . var_export($data, true) . ';');
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
        $file = $this->file($key);
        return file_exists($file) ? unlink($file) : false;
    }
}
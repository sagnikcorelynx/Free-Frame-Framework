<?php
use Core\Language;
if (!function_exists('config')) {
    /**
     * Get a configuration value using dot notation.
     *
     * @param string $key Example: 'app.name'
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        static $configs = [];

        // Load only once
        if (empty($configs)) {
            foreach (glob(__DIR__ . '/../../config/*.php') as $file) {
                $name = basename($file, '.php');
                $configs[$name] = require $file;
            }
        }

        $keys = explode('.', $key);
        $value = $configs;

        foreach ($keys as $segment) {
            if (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return $default;
            }
        }

        return $value;
    }
}

if (!function_exists('env')) {
    /**
     * Get environment variable or fallback value
     *
     * @param string $key e.g. 'APP_NAME'
     * @param mixed $default fallback if key not found or empty
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        static $loaded = false;
        static $envData = [];
        if (!$loaded) {
            $path = __DIR__ . '../../.env';
            if (file_exists($path)) {
                $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos(trim($line), '#') === 0) {
                        continue; // skip comments
                    }
                    [$name, $value] = array_map('trim', explode('=', $line, 2) + [1 => null]);
                    if ($name) {
                        $envData[$name] = trim($value, " \t\n\r\0\x0B\"");
                    }
                }
            }
            $loaded = true;
        }
        // Check in $_ENV, getenv(), then loaded .env data
        $value = $_ENV[$key] ?? getenv($key) ?: ($envData[$key] ?? null);
        if ($value === null || $value === '') {
            return $default;
        }
        return $value;
    }
}

if (!function_exists('lang')) {
    function lang($key, $default = null)
    {
        return Language::get($key, $default);
    }
}
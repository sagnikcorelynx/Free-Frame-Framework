<?php
namespace Core;

class Language
{
    protected static $locale;
    protected static $messages = [];

    public static function setLocale($locale)
    {
        self::$locale = $locale;
        $path = __DIR__ . '/../lang/' . $locale . '.php';
        if (file_exists($path)) {
            self::$messages = require $path;
        } else {
            throw new \Exception("Language file [$locale] not found.");
        }
    }

    public static function get($key, $default = null)
    {
        return self::$messages[$key] ?? $default ?? $key;
    }

    public static function locale()
    {
        return self::$locale;
    }
}
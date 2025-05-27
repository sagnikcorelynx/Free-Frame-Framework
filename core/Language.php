<?php
namespace Core;

class Language
{
    protected static $locale;
    protected static $messages = [];

    /**
     * Set the locale for the language.
     *
     * @param string $locale
     *
     * @throws \Exception
     */
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

    /**
     * Get the translation for a given key.
     *
     * @param  string  $key
     * @param  string  $default
     * @return string
     */
    public static function get($key, $default = null)
    {
        return self::$messages[$key] ?? $default ?? $key;
    }

    /**
     * Get the current locale
     *
     * @return string
     */
    public static function locale()
    {
        return self::$locale;
    }
}
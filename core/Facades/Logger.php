<?php

namespace Core\Facades;

use Core\Logger\Logger as LoggerInstance;

class Logger
{
    protected static $logger;

    /**
     * Get the logger instance.
     *
     * @return \Core\Logger\Logger
     */
    protected static function getLogger()
    {
        if (!self::$logger) {
            self::$logger = new LoggerInstance();
        }

        return self::$logger;
    }

    /**
     * Logs an information message.
     *
     * @param string $message
     *
     * @return void
     */
    public static function info($message)
    {
        self::getLogger()->log('INFO', $message);
    }

    /**
     * Logs an error message.
     *
     * @param string $message
     *
     * @return void
     */
    public static function error($message)
    {
        self::getLogger()->log('ERROR', $message);
    }

    /**
     * Logs a debug message.
     *
     * @param string $message
     *
     * @return void
     */
    public static function debug($message)
    {
        self::getLogger()->log('DEBUG', $message);
    }

    /**
     * Logs a warning message.
     *
     * @param string $message
     *
     * @return void
     */

    public static function warning($message)
    {
        self::getLogger()->log('WARNING', $message);
    }
}
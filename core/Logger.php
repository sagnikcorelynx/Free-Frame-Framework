<?php

namespace App\Core;

class Logger
{
    protected static $logFile = __DIR__ . '/../../Storage/Logs/error.log';

    public static function log($level, $message, $context = [])
    {
        $date = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        $logMessage = "[{$date}] {$level}: {$message} {$contextString}" . PHP_EOL;

        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }

    public static function error($message, $context = [])
    {
        self::log('ERROR', $message, $context);
    }

    public static function exception(\Throwable $e)
    {
        self::log('EXCEPTION', $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}
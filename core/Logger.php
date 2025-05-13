<?php

namespace App\Core;

class Logger
{
    protected static $logFile = __DIR__ . '/../../Storage/Logs/error.log';

    public static function error($message, $context = [])
    {
        $date = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? json_encode($context) : '';
        $logMessage = "[{$date}] ERROR: {$message} {$contextString}" . PHP_EOL;

        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }

    public static function exception(\Throwable $e)
    {
        self::error($e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

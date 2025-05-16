<?php

namespace Core;

class Debugger
{
    public static function register()
    {
        ini_set('display_errors', APP_DEBUG ? '1' : '0');
        error_reporting(E_ALL);
        
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        if (APP_DEBUG) {
            self::render('Error', compact('errno', 'errstr', 'errfile', 'errline'));
        } else {
            self::log("Error [$errno]: $errstr in $errfile on line $errline");
        }
    }

    public static function handleException($exception)
    {
        if (APP_DEBUG) {
            self::render('Exception', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        } else {
            self::log($exception);
        }
    }

    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            if (APP_DEBUG) {
                self::render('Fatal Error', $error);
            } else {
                self::log($error);
            }
        }
    }

    protected static function render($type, $data)
    {
        echo "<h1>$type</h1><pre>" . print_r($data, true) . "</pre>";
    }

    protected static function log($error)
    {
        $log = __DIR__ . '/../storage/Logs/error.log';
        error_log(date('[Y-m-d H:i:s] ') . print_r($error, true) . PHP_EOL, 3, $log);
    }
}

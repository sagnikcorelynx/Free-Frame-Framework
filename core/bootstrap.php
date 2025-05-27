<?php

// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

$dotenvPath = __DIR__ . '/../.env';


use Core\Language;
// Load environment variables from .env file
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#')) continue;

        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}
Language::setLocale(config('app.locale', 'en'));
// Load environment or config settings
$config = require_once __DIR__ . '/../core/config.php';
define('APP_DEBUG', $config['debug']);
define('APP_ENV', $config['env']);
use Core\Logger;
use Core\Debugger;

// Set error handler for warnings, notices, etc.
set_error_handler(function ($severity, $message, $file, $line) {
    Logger::error($message, compact('severity', 'file', 'line'));
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Set exception handler for uncaught exceptions
set_exception_handler(function ($exception) {
    Logger::exception($exception);
    http_response_code(500);
    echo "500 | Internal Server Error. Check logs.";
});

// Handle fatal errors like parse errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        Logger::log('FATAL', $error['message'], [
            'file' => $error['file'],
            'line' => $error['line']
        ]);
        http_response_code(500);
        echo "500 | Fatal error occurred. Check logs.";
    }
});

Debugger::register();

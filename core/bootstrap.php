<?php

// Autoload dependencies
require_once __DIR__ . '/../vendor/autoload.php';

$dotenvPath = __DIR__ . '/../.env';
// Load environment variables from .env file
if (file_exists($dotenvPath)) {
    $lines = file($dotenvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_contains(trim($line), '#')) continue;
        list($key, $value) = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}
// Load environment or config settings

use App\Core\Logger;

// Register error handler
set_error_handler(function ($severity, $message, $file, $line) {
    Logger::error($message, compact('severity', 'file', 'line'));
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// Register exception handler
set_exception_handler(function ($exception) {
    App\Core\Logger::exception($exception);
    http_response_code(500);
    echo "An unexpected error occurred. Check logs.";
});

// Optional: Shutdown handler for fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        App\Core\Logger::error($error['message'], [
            'file' => $error['file'],
            'line' => $error['line']
        ]);
    }
});

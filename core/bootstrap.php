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

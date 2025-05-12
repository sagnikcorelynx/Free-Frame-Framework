<?php

// Autoload Composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Include your framework's bootstrap process
require_once __DIR__ . '/../core/bootstrap.php';

// Run the application (you can set up your request routing here)
$request = $_SERVER['REQUEST_URI'];

// Example: Route requests
if ($request === '/' || $request === '/home') {
    echo 'Welcome to FreeFrame!';
} else {
    echo 'Page not found.';
}

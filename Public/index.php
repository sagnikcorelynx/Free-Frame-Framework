<?php

// Autoload Composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Load your bootstrap logic (env vars, config, etc.)
require_once __DIR__ . '/../core/bootstrap.php';

// Load the defined routes and get an instance of Router
$router = require_once __DIR__ . '/../routes/route.php';

// Get the current URI and method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Dispatch the request
$response = $router->dispatch($uri, $method);


// If a response is returned, display it
if ($response !== null) {
    echo $response;
} else {
    // If no route matches, fallback
    if ($uri == '/') {
        echo <<<HTML
        <div style="display: flex; align-items: center; justify-content: center; height: 100vh;">
            <div style="text-align: center;">
                <img src="/assets/logo.webp" width="50%" height="50%" alt="FreeFrame Logo">
                <h1 style="font-size: 3em; color: #3a9cff;">Welcome to <span style="font-weight: bold;">FreeFrame</span>!</h1>
                <p style="font-size: 1.5em;">A lightweight PHP framework with a custom CLI, inspired by Laravel and CakePHP.</p>
            </div>
        </div>
        HTML;
    } else {
        http_response_code(404);
    }
}
// Handle any exceptions
set_exception_handler(function ($exception) {
    http_response_code(500);
    echo '500 - Internal Server Error: ' . $exception->getMessage();
});
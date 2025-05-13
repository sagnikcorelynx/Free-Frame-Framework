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
    if($uri == '/'){
        return print('Welcome to FreeFrame!');
    }else{
        return http_response_code(404);
    }
    
}
// Handle any exceptions
set_exception_handler(function ($exception) {
    http_response_code(500);
    echo '500 - Internal Server Error: ' . $exception->getMessage();
});
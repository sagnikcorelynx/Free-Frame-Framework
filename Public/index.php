<?php

// Autoload Composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Load your bootstrap logic (env vars, config, etc.)
require_once __DIR__ . '/../core/bootstrap.php';

// Load the defined routes and get an instance of Router
$router = require_once __DIR__ . '/../routes/route.php';

use Core\Http\Request;
use App\Middleware\Kernel; // ← Make sure this is correct
use Core\Http\Response;

// Create request object
$request = new Request();

// Setup exception handler
set_exception_handler(function ($exception) {
    http_response_code(500);
    echo '500 - Internal Server Error: ' . $exception->getMessage();
});

// Get the current URI and method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Kernel middleware execution
$kernel = new Kernel();

$routeMatch = $router->match($uri, $method);

if ($routeMatch) {
    $routeMiddleware = $routeMatch->getMiddleware() ?? [];

    $response = $kernel->handle($request, function ($request) use ($routeMatch) {
        $controller = new ($routeMatch->controller)();
        $action = $routeMatch->method;
        $content = $controller->$action($request);

        return new Response($content);
    }, $routeMiddleware);
} else {
    // No matched route fallback
    $response = $kernel->handle($request, function () use ($uri) {
        if ($uri === '/') {
            $html = <<<HTML
                <div style="display: flex; align-items: center; justify-content: center; height: 100vh;">
                    <div style="text-align: center;">
                        <img src="/assets/logo.webp" width="50%" height="50%" alt="FreeFrame Logo">
                        <h1 style="font-size: 3em; color: #3a9cff;">Welcome to <span style="font-weight: bold;">FreeFrame</span>!</h1>
                        <p style="font-size: 1.5em;">A lightweight PHP framework with a custom CLI.</p>
                    </div>
                </div>
            HTML;
            return new Response($html);
        }
        $html = <<<HTML
            <div style="display: flex; align-items: center; justify-content: center; height: 100vh;">
                <div style="text-align: center;">
                    <h1 style="font-size: 3em; color: #3a9cff;">404 not found...!</h1>
                    <p style="font-size: 1.5em;">The requested page could not be found.</p>
                </div>
            </div>
            HTML;
        return new Response($html, 404);
    });
}

// Send final response to browser
$response->send();
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
                <div style="display: flex; align-items: center; justify-content: center; height: 100vh; background-color:rgb(232, 238, 219);">
                    <div style="text-align: center; width: 500px; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 4px rgba(0, 0, 0, 5.1);">
                        <img src="/assets/logo.webp" width="200px" height="200px" alt="FreeFrame Logo" style="margin-bottom: 20px;">
                        <h1 style="font-size: 2em; color: #3a9cff; margin-bottom: 20px;">Welcome to <span style="font-weight: bold;">FreeFrame</span>!</h1>
                        <p style="font-size: 1.5em; margin-bottom: 20px; text-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);">A lightweight PHP framework with a custom CLI.</p>
                        <pre style="font-size: 1.2em; text-align: left; background-color: #222; padding: 10px; border-radius: 10px; overflow: auto; max-height: 600px;">
                            <span style="color: #999;">$ </span><span style="color: #ff6600;">free</span> <span style="color: #66d9ef;">help</span>
                            <span style="color: #999;">$ </span><span style="color: #ff6600;">free</span> <span style="color: #66d9ef;">make:</span><span style="color: #a6e22e;">controller</span> <span style="color: #a6e22e;">Name</span>
                            <span style="color: #999;">$ </span><span style="color: #ff6600;">free</span> <span style="color: #66d9ef;">make:</span><span style="color: #a6e22e;">model</span> <span style="color: #a6e22e;">Name</span>
                            <span style="color: #999;">$ </span><span style="color: #ff6600;">free</span> <span style="color: #66d9ef;">make:</span><span style="color: #a6e22e;">request</span> <span style="color: #a6e22e;">Name</span>
                            <span style="color: #999;">$ </span><span style="color: #ff6600;">free</span> <span style="color: #66d9ef;">make:</span><span style="color: #a6e22e;">migration</span> <span style="color: #a6e22e;">Name</span>
                        </pre>
                        <p style="font-size: 1.2em; color: #999;">
                            FreeFrame is a lightweight, modular PHP framework, built for rapid development with minimal setup. It comes with its own powerful CLI tool named `free`, allowing you to scaffold components, manage your project structure, and streamline development.
                        </p>
                        <p style="font-size: 1em; color: #999;">
                            If you have any questions or need help with FreeFrame, please don't hesitate to contact me at <a href="mailto:sagnikcoold@gmail.com" style="color: #999;">sagnikcoold@gmail.com</a>.
                        </p>
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
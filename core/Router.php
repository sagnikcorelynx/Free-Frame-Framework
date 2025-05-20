<?php

namespace Core;

use Closure;

class Router
{
    protected array $routes = [];

    /**
     * Register a GET route.
     *
     * @param string $uri
     * @param mixed $action
     * @param array $middleware
     * @return void
     */
    public function get(string $uri, $action, array $middleware = []): void
    {
        $this->addRoute('GET', $uri, $action, $middleware);
    }

    /**
     * Register a POST route.
     *
     * @param string $uri
     * @param mixed $action
     * @param array $middleware
     * @return void
     */
    public function post(string $uri, $action, array $middleware = []): void
    {
        $this->addRoute('POST', $uri, $action, $middleware);
    }

    /**
     * Add a route to the router.
     *
     * @param string $method HTTP method for the route.
     * @param string $uri URI for the route.
     * @param mixed $action Callable action to be executed when the route is requested.
     * @param array $middleware List of middleware to be executed before the action.
     *
     * @return void
     */
    protected function addRoute(string $method, string $uri, $action, array $middleware): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => trim($uri, '/'),
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    /**
     * Dispatch the request to the appropriate route handler.
     *
     * @param string $requestUri Request URI to be routed.
     * @param string $requestMethod HTTP method of the request.
     *
     * @return void
     */
    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $uri = trim(parse_url($requestUri, PHP_URL_PATH), '/');
        $method = strtoupper($requestMethod);
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                $handler = $route['action'];
                $middlewareList = $route['middleware'] ?? [];

                if (!empty($middlewareList)) {
                    $middlewareHandler = new MiddlewareHandler();
                    foreach ($middlewareList as $middleware) {
                        $middlewareHandler->add($middleware);
                    }

                    $middlewareHandler->handle(
                        $_REQUEST,
                        function ($request) use ($handler) {
                            return $this->executeHandler($handler, $request);
                        }
                    );
                    return;
                }

                $this->executeHandler($handler, $_REQUEST);
                return;
            }
        }

        http_response_code(404);
        if ($uri === '') {
            // Handle base URL
        } else {
            $html = <<<HTML
            <div style="display: flex; align-items: center; justify-content: center; height: 100vh;">
                <div style="text-align: center;">
                    <h1 style="font-size: 3em; color: #3a9cff;">404 not found...!</h1>
                    <p style="font-size: 1.5em;">The requested page could not be found.</p>
                </div>
            </div>
            HTML;
            echo $html;
        }
    }

    protected function executeHandler($handler, $request)
    {
        if (is_callable($handler)) {
            return $handler($request);
        }

        // [ControllerClass, method] format
        if (is_array($handler) && count($handler) === 2) {
            [$controller, $method] = $handler;
            if (class_exists($controller)) {
                $instance = new $controller();
                return call_user_func([$instance, $method], $request);
            }
        }

        throw new \Exception("Invalid route handler.");
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
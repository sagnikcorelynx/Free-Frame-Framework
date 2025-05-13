<?php
namespace Core;

class Router {
    protected $routes = [];

    public function get($uri, $callback) {
        $this->routes['GET'][$uri] = $callback;
    }

    public function post($uri, $callback) {
        $this->routes['POST'][$uri] = $callback;
    }

    public function dispatch($uri, $method) {
        $uri = rtrim($uri, '/'); // remove trailing slashes
        $callback = $this->routes[$method][$uri] ?? null;

        if (!$callback) {
            http_response_code(404);
            echo "404 - Route Not Found";
            return;
        }

        if (is_callable($callback)) {
            return call_user_func($callback);
        }

        if (is_string($callback)) {
            // Example: 'HomeController@index'
            [$controller, $action] = explode('@', $callback);
            $controller = 'App\\Controllers\\' . $controller;

            if (class_exists($controller)) {
                $obj = new $controller();
                return call_user_func([$obj, $action]);
            } else {
                http_response_code(500);
                echo "Controller $controller not found.";
                return;
            }
        }
    }
}
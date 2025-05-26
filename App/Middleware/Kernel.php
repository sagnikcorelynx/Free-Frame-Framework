<?php

namespace App\Middleware;

use Core\Http\Request;
use Core\Contracts\MiddlewareInterface;

class Kernel
{
    protected array $middleware = [
        \App\Middleware\CorsMiddleware::class,
        // Add more global middleware here...
    ];

    // Middleware groups (e.g. web, api, auth)
    protected array $middlewareGroups = [
        'web' => [
            \App\Middleware\SessionMiddleware::class
        ],
        'api' => [
            \App\Middleware\RateLimitMiddleware::class,
        ],
        'auth' => [
            \App\Middleware\AuthMiddleware::class,
        ],
    ];

     /**
     * Handle global and route middleware.
     */
    public function handle(Request $request, \Closure $callback, array $routeMiddleware = [])
    {
        $middleware = array_merge(
            $this->middleware,
            $this->resolveMiddlewareGroups($routeMiddleware)
        );

        $pipeline = array_reduce(
            array_reverse($middleware),
            fn ($next, $middlewareClass) => fn ($req) => (new $middlewareClass())->handle($req, $next),
            $callback
        );

        return $pipeline($request);
    }

    /**
     * Resolve group and individual middleware.
     */
    protected function resolveMiddlewareGroups(array $middleware): array
    {
        $resolved = [];

        foreach ($middleware as $item) {
            if (isset($this->middlewareGroups[$item])) {
                $resolved = array_merge($resolved, $this->middlewareGroups[$item]);
            } else {
                $resolved[] = $item;
            }
        }

        return $resolved;
    }

    
}

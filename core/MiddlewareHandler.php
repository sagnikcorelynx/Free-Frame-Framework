<?php

namespace Core;

use Closure;

class MiddlewareHandler
{
    protected array $middlewareStack = [];

    /**
     * Add a middleware to the stack.
     *
     * @param mixed $middleware
     * @return void
     */
    public function add($middleware)
    {
        $this->middlewareStack[] = $middleware;
    }

    /**
     * Processes the request through a chain of middleware.
     *
     * @param mixed $request The incoming request to be processed by middleware.
     * @param Closure $destination The final destination closure to be called after all middleware.
     * @return mixed The response after the middleware chain has been processed.
     */

    public function handle($request, Closure $destination)
    {
        $middlewareChain = array_reduce(
            array_reverse($this->middlewareStack),
            function ($next, $middleware) {
                return function ($request) use ($next, $middleware) {
                    $instance = new $middleware;
                    return $instance->handle($request, $next);
                };
            },
            $destination
        );

        return $middlewareChain($request);
    }
}

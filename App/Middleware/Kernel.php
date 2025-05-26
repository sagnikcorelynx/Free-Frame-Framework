<?php

namespace App\Middleware;

use Core\Http\Request;

class Kernel
{
    protected array $middleware = [
        \App\Middleware\CorsMiddleware::class,
        // Add more global middleware here...
    ];

    public function handle(Request $request, \Closure $callback)
    {
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            fn ($next, $middlewareClass) => fn ($req) => (new $middlewareClass())->handle($req, $next),
            $callback
        );

        return $pipeline($request);
    }
}

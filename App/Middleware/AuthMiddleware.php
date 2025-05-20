<?php

namespace App\Middleware;

use Closure;
use Core\Contracts\MiddlewareInterface;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle($request, Closure $next)
    {
        // Add your logic here

        return $next($request);
    }
}
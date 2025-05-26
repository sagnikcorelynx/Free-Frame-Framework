<?php

namespace App\Middleware;

use Closure;
use Core\Contracts\MiddlewareInterface;
use Core\Http\Request;
use Core\Http\Response;

class SessionMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Closure $next)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // You can attach session data to request if you want
        // e.g. $request->session = &$_SESSION;
        $response = $next($request);
        // session_write_close();
        return $response;
    }
}
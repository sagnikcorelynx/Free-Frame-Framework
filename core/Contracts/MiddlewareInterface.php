<?php

namespace Core\Contracts;
use Core\Http\Request;
use Closure;

interface MiddlewareInterface
{
    public function handle(Request $request, Closure $next);
}

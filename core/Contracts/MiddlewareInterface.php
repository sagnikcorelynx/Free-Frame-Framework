<?php

namespace Core\Contracts;

use Closure;

interface MiddlewareInterface
{
    public function handle($request, Closure $next);
}

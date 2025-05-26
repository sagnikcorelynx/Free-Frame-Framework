<?php

namespace App\Middleware;

use Closure;
use Core\Contracts\MiddlewareInterface;
use Core\Http\Request;
use Core\Http\Response;

class RateLimitMiddleware implements MiddlewareInterface
{
    // Path to the JSON file storing request counts and expiry timestamps
    protected string $rateLimitFile;

    // Maximum requests allowed per time window
    protected int $maxRequests = 10;

    // Time window duration in seconds (e.g., 60 seconds = 1 minute)
    protected int $timeWindow = 60;

    public function __construct()
    {
        // Adjust this path as needed (root directory + Storage/Cache folder, for example)
        $this->rateLimitFile = __DIR__ . '/../../Storage/Cache/rate_limit.json';

        // Create file if it doesn't exist to avoid errors
        if (!file_exists($this->rateLimitFile)) {
            file_put_contents($this->rateLimitFile, json_encode([]));
        }
    }

    public function handle(Request $request, Closure $next)
    {
        $ip = $request->getIp(); // Assumes your Request class has getIp() method
        $now = time();

        // Load current data from JSON file
        $data = json_decode(file_get_contents($this->rateLimitFile), true);
        if (!is_array($data)) {
            $data = [];
        }

        // If this IP is not tracked or the time window expired, reset count and expiry
        if (!isset($data[$ip]) || $data[$ip]['expires_at'] < $now) {
            $data[$ip] = [
                'count' => 1,
                'expires_at' => $now + $this->timeWindow,
            ];
        } else {
            // If count exceeded limit, return 429 Too Many Requests response
            if ($data[$ip]['count'] >= $this->maxRequests) {
                $retryAfter = $data[$ip]['expires_at'] - $now;

                $response = new Response('Too Many Requests', 429);
                $response->header('Retry-After', $retryAfter);
                return $response;
            }

            // Otherwise increment count
            $data[$ip]['count']++;
        }

        // Save updated data back to JSON file
        file_put_contents($this->rateLimitFile, json_encode($data));

        // Continue to next middleware / request handling
        return $next($request);
    }
}
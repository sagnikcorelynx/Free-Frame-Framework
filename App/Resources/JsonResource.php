<?php

namespace App\Resources;

class JsonResource
{
    protected $resource;

    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * Customize how the resource is transformed to an array
     * Override this method in child classes for custom formatting
     */
    public function toArray(): array
    {
        // Default: cast resource to array
        if (is_array($this->resource)) {
            return $this->resource;
        }

        if (is_object($this->resource) && method_exists($this->resource, 'toArray')) {
            return $this->resource->toArray();
        }

        // Fallback: convert public properties to array
        return (array) $this->resource;
    }

    /**
     * Return the JSON encoded response with a standard structure
     */
    public function toJson(int $options = 0): string
    {
        $response = [
            'success' => true,
            'data' => $this->toArray(),
            'message' => '',
            'errors' => null,
            'meta' => null,
        ];

        return json_encode($response, $options);
    }

    /**
     * Send the JSON response with HTTP headers and exit
     */
    public function send(int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo $this->toJson(JSON_UNESCAPED_UNICODE);
        exit;
    }
}
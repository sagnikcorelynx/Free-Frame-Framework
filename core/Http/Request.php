<?php
namespace Core\Http;

class Request
{
    protected $get;
    protected $post;
    protected $files;
    protected $server;
    protected $jsonPayload;

    /**
     * Constructor. Sets up the request object by populating the get, post, files and server properties.
     *
     * Also checks if the request is a JSON request and sets the jsonPayload property
     * accordingly.
     */
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;

        // Parse JSON body if content-type is application/json
        if ($this->server['CONTENT_TYPE'] ?? '' === 'application/json') {
            $this->jsonPayload = json_decode(file_get_contents('php://input'), true) ?? [];
        } else {
            $this->jsonPayload = [];
        }
    }

    /**
     * Retrieves the value of a given key from the current request.
     * Checks in the following order:
     * 1. $_POST
     * 2. $_GET
     * 3. JSON payload
     *
     * If the key is not found in any of the above, returns the $default value.
     *
     * @param string $key The key to retrieve
     * @param mixed $default The default value to return if the key is not found
     *
     * @return mixed The retrieved value
     */
    public function input(string $key, $default = null)
    {
        if (isset($this->post[$key])) {
            return $this->post[$key];
        }
        if (isset($this->get[$key])) {
            return $this->get[$key];
        }
        if (isset($this->jsonPayload[$key])) {
            return $this->jsonPayload[$key];
        }
        return $default;
    }

    /**
     * Retrieves all the request data in a single array. Merges $_GET, $_POST and the JSON payload
     * (if any) into a single associative array.
     *
     * @return array The merged request data
     */
    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->jsonPayload);
    }

    /**
     * Checks if a given key exists in the current request. Checks in the following order:
     * 1. $_POST
     * 2. $_GET
     * 3. JSON payload
     *
     * @param string $key The key to search for
     * @return bool True if the key is found, false otherwise
     */
    public function has(string $key): bool
    {
        return isset($this->post[$key]) || isset($this->get[$key]) || isset($this->jsonPayload[$key]);
    }

    /**
     * Retrieves a file from the request by key. If the key does not exist, or no file was uploaded,
     * returns null.
     *
     * @param string $key The key of the file
     * @return array|null The $_FILES array for the given key, or null if no file was uploaded
     */
    public function file(string $key)
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Retrieves the HTTP method of the current request.
     *
     * @return string The HTTP method, or 'GET' if the request method is not set.
     */
    public function method(): string
    {
        return $this->server['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Retrieves the URI of the current request.
     *
     * @return string The URI path or '/' if the request URI is not set.
     */

    public function uri(): string
    {
        return parse_url($this->server['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    }

    public function getIp(): string
    {
        // Check for shared internet/ISP IP
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        // Check for IPs passed from proxies
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Sometimes multiple IPs passed, take the first one
            $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ipList[0]);
        }

        // Default remote IP address
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
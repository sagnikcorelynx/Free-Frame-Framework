<?php
namespace Core\Http;

class HttpRemote
{
    
    /**
     * Constructor for the HttpRemote class.
     *
     * Initializes a new instance of the HttpRemote class.
     */

    public function __construct()
    {
        
    }
    /**
     * Sends a GET request to the given URL and returns the response as a string, or false on failure.
     *
     * @param string $url The URL to request
     * @param array $headers The headers to include in the request
     *
     * @return string|false The response string, or false on failure
     */
    public function get(string $url, array $headers = []): string|false
    {
        return $this->sendRequest('GET', $url, [], $headers);
    }

    /**
     * Sends a POST request to the given URL with the given data and returns the response as a string, or false on failure.
     *
     * @param string $url The URL to request
     * @param array $data The data to send in the request body
     * @param array $headers The headers to include in the request
     *
     * @return string|false The response string, or false on failure
     */
    public function post(string $url, array $data = [], array $headers = []): string|false
    {
        return $this->sendRequest('POST', $url, $data, $headers);
    }

    /**
     * Sends a PUT request to the given URL with the provided data and returns the response as a string, or false on failure.
     *
     * @param string $url The URL to send the request to
     * @param array $data The data to include in the request body
     * @param array $headers The headers to include in the request
     *
     * @return string|false The response string, or false on failure
     */

    public function put(string $url, array $data = [], array $headers = []): string|false
    {
        return $this->sendRequest('PUT', $url, $data, $headers);
    }

    /**
     * Sends a DELETE request to the given URL with the provided data and returns the response as a string, or false on failure.
     *
     * @param string $url The URL to send the request to
     * @param array $data The data to include in the request body
     * @param array $headers The headers to include in the request
     *
     * @return string|false The response string, or false on failure
     */
    public function delete(string $url, array $data = [], array $headers = []): string|false
    {
        return $this->sendRequest('DELETE', $url, $data, $headers);
    }

    /**
     * Sends an HTTP request to the given URL with the given data and returns the response as a string, or false on failure.
     *
     * @param string $method The HTTP method to use (GET, POST, PUT, DELETE, etc.)
     * @param string $url The URL to send the request to
     * @param array $data The data to include in the request body (if applicable)
     * @param array $headers The headers to include in the request
     *
     * @return string|false The response string, or false on failure
     */
    private function sendRequest(string $method, string $url, array $data = [], array $headers = []): string|false
    {
        $ch = curl_init();

        if (in_array($method, ['POST', 'PUT', 'DELETE']) && !empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        $response = curl_exec($ch);
        $error    = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return false;
        }

        return $response;
    }
}
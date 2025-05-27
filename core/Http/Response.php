<?php
namespace Core\Http;

class Response
{
    protected $content;
    protected $status = 200;
    protected $headers = [];

    /**
     * Constructor.
     *
     * @param string $content The content to send as the response body
     * @param int $status The HTTP status code to use (default: 200)
     * @param array $headers Additional HTTP headers to include in the response
     *
     * @return self
     */
    public function __construct($content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * Sets a single HTTP header in the response.
     * 
     * @param string $key The header key
     * @param string $value The header value
     * 
     * @return self The response object, for chaining
     */
    public function header(string $key, string $value): self
    {
        header("$key: $value", true);
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Sends the HTTP response to the client.
     * 
     * Sets the HTTP status code, applies all headers, and outputs the response content.
     * This method should be called after the response is fully constructed.
     */

    public function send()
    {
        http_response_code($this->status);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        echo $this->content;
    }

    /**
     * Creates a JSON response.
     *
     * @param mixed $data The data to output as JSON
     * @param int $status The HTTP status code (default: 200)
     * @param array $headers Additional HTTP headers (default: empty array)
     *
     * @return self
     */
    public static function json($data, int $status = 200, array $headers = []): self
    {
        $headers['Content-Type'] = 'application/json';
        $content = json_encode($data);

        return new static($content, $status, $headers);
    }

    /**
     * Creates a response that sends a file to the client.
     *
     * @param string $filePath The path to the file to send
     * @param string $downloadName The name of the file as it should be saved on the client (default: the basename of $filePath)
     *
     * @return self
     */
    public static function file(string $filePath, string $downloadName = null): self
    {
        $content = file_get_contents($filePath);
        $headers = [
            'Content-Type' => mime_content_type($filePath),
            'Content-Disposition' => 'attachment; filename="' . ($downloadName ?? basename($filePath)) . '"',
        ];
        return new static($content, 200, $headers);
    }
}
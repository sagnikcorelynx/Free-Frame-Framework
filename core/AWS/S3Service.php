<?php
namespace Core\AWS;

use Aws\S3\S3Client;

class S3Service
{
    protected $client;
    protected $bucket;

    public function __construct()
    {
        $this->bucket = env('AWS_S3_BUCKET');
        $this->client = new S3Client([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    /**
     * Uploads a file to S3
     *
     * @param string $key The key of the object
     * @param string $body The body of the object
     * @param string $contentType The content type of the object (default: application/octet-stream)
     *
     * @return \Aws\ResultInterface
     */
    public function upload($key, $body, $contentType = 'application/octet-stream')
    {
        return $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'Body' => $body,
            'ContentType' => $contentType,
        ]);
    }

    
    /**
     * Fetches a media file from S3
     * 
     * @param string $key The key of the object
     * @param string $type The type of the object (default: application/octet-stream)
     * 
     * @return \Aws\ResultInterface
     */
    public function fetch($key, $type = 'application/octet-stream')
    {
        return $this->client->getObject([
            'Bucket' => $this->bucket,
            'Key' => $key,
            'ContentType' => $type,
        ]);
    }
}
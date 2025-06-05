<?php

namespace Core\AWS;

use Aws\Sqs\SqsClient;

class SQSService
{
    protected $client;
    protected $queueUrl;

    public function __construct()
    {
        $this->queueUrl = env('AWS_SQS_QUEUE_URL');
        $this->client = new SqsClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    /**
     * Send a message to the SQS queue
     *
     * @param string $message The message to send
     *
     * @return \Aws\Result
     */
    public function sendMessage($message)
    {
        return $this->client->sendMessage([
            'QueueUrl' => $this->queueUrl,
            'MessageBody' => $message,
        ]);
    }

    
    /**
     * Log a message to CloudWatch
     *
     * @param string $message The message to log
     * @param string $group The log group to log to
     * @param string $stream The log stream to log to
     * @return \Aws\Result
     */
    public function log($message, $group = 'freeframe', $stream = 'freeframe')
    {
        $client = new \Aws\CloudWatchLogs\CloudWatchLogsClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

        return $client->createLogEvent([
            'logGroupName' => $group,
            'logStreamName' => $stream,
            'logEvents' => [
                [
                    'timestamp' => time() * 1000,
                    'message' => $message
                ]
            ]
        ]);
    }
}
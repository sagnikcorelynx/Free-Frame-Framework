<?php
namespace Core\AWS;

use Aws\Sns\SnsClient;

class SNSService
{
    protected $client;
    protected $topicArn;

    public function __construct()
    {
        $this->topicArn = env('AWS_SNS_TOPIC_ARN');
        $this->client = new SnsClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    /**
     * Publishes a message to the SNS topic.
     *
     * @param string $message The message to publish
     *
     * @return \Aws\Result
     */
    public function publish($message)
    {
        return $this->client->publish([
            'TopicArn' => $this->topicArn,
            'Message' => $message,
        ]);
    }

    
    /**
     * Publishes a message to the SNS topic.
     *
     * @param string $eventName The event name to publish
     * @param array $data The event data to publish
     *
     * @return \Aws\Result
     */
    public function event($eventName, $data)
    {
        return $this->client->publish([
            'TopicArn' => $this->topicArn,
            'Message' => json_encode([
                'event' => $eventName,
                'data' => $data,
            ]),
        ]);
    }
}
<?php

namespace Core\AWS;

use Aws\Ses\SesClient;

class SESService
{
    protected $client;

    public function __construct()
    {
        $this->client = new SesClient([
            'version' => 'latest',
            'region' => env('AWS_DEFAULT_REGION'),
            'credentials' => [
                'key' => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);
    }

    /**
     * Sends an email using the Simple Email Service (SES) of AWS.
     *
     * @param string $to The recipient of the email.
     * @param string $subject The subject of the email.
     * @param string $htmlBody The body of the email in HTML format.
     *
     * @return \Aws\Result
     */
    public function sendEmail($to, $subject, $htmlBody)
    {
        return $this->client->sendEmail([
            'Source' => env('AWS_SES_EMAIL'),
            'Destination' => [
                'ToAddresses' => [$to],
            ],
            'Message' => [
                'Subject' => ['Data' => $subject],
                'Body' => [
                    'Html' => ['Data' => $htmlBody],
                ],
            ],
        ]);
    }
}
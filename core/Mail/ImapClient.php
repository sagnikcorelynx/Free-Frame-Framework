<?php

namespace Core\Mail;

use Webklex\IMAP\ClientManager;

class ImapClient
{
    protected $client;

    public function __construct()
    {
        $cm = new ClientManager('array');

        $this->client = $cm->make([
            'host'          => env('IMAP_HOST', 'imap.gmail.com'),
            'port'          => env('IMAP_PORT', 993),
            'encryption'    => env('IMAP_ENCRYPTION', 'ssl'),
            'validate_cert' => env('IMAP_VALIDATE_CERT', true),
            'username'      => env('IMAP_USERNAME'),
            'password'      => env('IMAP_PASSWORD'),
            'protocol'      => 'imap'
        ]);

        $this->client->connect();
    }

    /**
     * Get the messages in the inbox folder.
     *
     * @param int $limit The maximum number of messages to return
     *
     * @return \Webklex\IMAP\Support\Collection
     */
    public function getInboxMessages($limit = 10)
    {
        $folder = $this->client->getFolder('INBOX');
        return $folder->messages()->all()->limit($limit)->get();
    }

    /**
     * Get all the folders in the IMAP account.
     *
     * @return \Webklex\IMAP\Support\Collection
     */
    public function getFolders()
    {
        return $this->client->getFolders();
    }

    /**
     * Get the underlying IMAP client instance.
     *
     * @return \Webklex\IMAP\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    
    /**
     * Get the messages in the spam folder.
     *
     * @param int $limit The maximum number of messages to return
     *
     * @return \Webklex\IMAP\Support\Collection
     */
    public function getSpamMessages($limit = 10)
    {
        $folder = $this->client->getFolder('[Gmail]/Spam');
        return $folder->messages()->all()->limit($limit)->get();
    }

    
    /**
     * Get the messages in the sent folder.
     *
     * @param int $limit The maximum number of messages to return
     *
     * @return \Webklex\IMAP\Support\Collection
     */
    public function getSentMessages($limit = 10)
    {
        $folder = $this->client->getFolder('[Gmail]/Sent Mail');
        return $folder->messages()->all()->limit($limit)->get();
    }
}
<?php

namespace Core\AWS;

use Aws\Efs\EfsClient;

class EFSService
{
    protected $client;

    public function __construct()
    {
        $this->client = new EfsClient([
            'version' => 'latest',
            'region'  => 'your-region',
            'credentials' => [
                'key'    => 'your-access-key-id',
                'secret' => 'your-secret-access-key',
            ],
        ]);
    }

    // Create a new EFS file system
    public function createFileSystem($creationToken)
    {
        return $this->client->createFileSystem([
            'CreationToken' => $creationToken,
        ]);
    }

    // Describe EFS file systems
    public function describeFileSystems($fileSystemId = null)
    {
        $params = [];
        if ($fileSystemId) {
            $params['FileSystemId'] = $fileSystemId;
        }

        return $this->client->describeFileSystems($params);
    }

    // Delete an EFS file system
    public function deleteFileSystem($fileSystemId)
    {
        return $this->client->deleteFileSystem([
            'FileSystemId' => $fileSystemId,
        ]);
    }
}


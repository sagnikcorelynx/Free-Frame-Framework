<?php

namespace Core\Logger;

class Logger
{
    protected $logFile;

    public function __construct()
    {
        $this->logFile = __DIR__ . '/../../Storage/Logs/app.log';

        if (!file_exists(dirname($this->logFile))) {
            mkdir(dirname($this->logFile), 0755, true);
        }
    }

    public function log($level, $message)
    {
        $time = date('Y-m-d H:i:s');
        $entry = "[{$time}] {$level}: {$message}" . PHP_EOL;
        file_put_contents($this->logFile, $entry, FILE_APPEND);
    }
}
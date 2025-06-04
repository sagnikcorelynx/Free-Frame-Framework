<?php

namespace Core\Jobs;

class Queue
{
    protected static $queueFile = __DIR__ . '/../../../storage/queue/jobs.txt';

    public static function push(string $job)
    {
        file_put_contents(self::$queueFile, $job . PHP_EOL, FILE_APPEND);
    }

    public static function pop(): ?string
    {
        if (!file_exists(self::$queueFile)) {
            return null;
        }

        $jobs = file(self::$queueFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (empty($jobs)) {
            return null;
        }

        $job = array_shift($jobs);
        file_put_contents(self::$queueFile, implode(PHP_EOL, $jobs) . PHP_EOL);

        return trim($job);
    }
}
<?php

namespace Core\Jobs;

abstract class BaseJob implements JobInterface {
    public function dispatch() {
        $queueFile = STORAGE_PATH . '/Queue/jobs.json';
        $jobs = file_exists($queueFile) ? json_decode(file_get_contents($queueFile), true) : [];
        $jobs[] = get_class($this);
        file_put_contents($queueFile, json_encode($jobs));
    }
}
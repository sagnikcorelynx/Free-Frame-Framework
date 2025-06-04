<?php

namespace Core\Jobs;

class QueueWorker {
    public function run() {
        $queueFile = STORAGE_PATH . '/Queue/jobs.json';
        if (!file_exists($queueFile)) {
            echo "No jobs found.\n";
            return;
        }

        $jobs = json_decode(file_get_contents($queueFile), true);

        foreach ($jobs as $index => $jobClass) {
            if (!class_exists($jobClass)) {
                echo "Class $jobClass not found.\n";
                continue;
            }

            $job = new $jobClass();
            if ($job instanceof JobInterface) {
                $job->handle();
                echo "Processed job: $jobClass\n";
            } else {
                echo "Job does not implement JobInterface: $jobClass\n";
            }

            unset($jobs[$index]);
        }

        file_put_contents($queueFile, json_encode(array_values($jobs)));
    }
}
<?php

namespace App\Commands;

use Core\Jobs\Queue;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class QueueWorkCommand extends Command
{
    protected static $defaultName = 'queue:work';

    /**
     * Gets the default name for the command.
     *
     * @return string|null The default name for the command.
     */
    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this->setDescription('Process jobs from the queue');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Queue worker started. Waiting for jobs...</info>');

        while (true) {
            $job = Queue::pop();

            if ($job) {
                $output->writeln("<comment>Processing job: {$job}</comment>");

                include_once base_path("jobs/{$job}.php");

                if (class_exists($job)) {
                    $jobInstance = new $job();
                    if (method_exists($jobInstance, 'handle')) {
                        try {
                            $jobInstance->handle();
                            $output->writeln("<info>Job {$job} processed successfully.</info>");
                        } catch (\Exception $e) {
                            $output->writeln("<error>Error processing job {$job}: {$e->getMessage()}</error>");
                        }
                    } else {
                        $output->writeln("<error>Method handle() not found in job class {$job}</error>");
                    }
                } else {
                    $output->writeln("<error>Class {$job} not found.</error>");
                }
            } else {
                sleep(1); // prevent CPU 100%
            }
        }

        return Command::SUCCESS;
    }
}
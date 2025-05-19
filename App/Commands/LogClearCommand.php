<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogClearCommand extends Command
{
    protected static $defaultName = 'log:clear';
    protected static $defaultDescription = 'Clear the error log file.';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $logPath = __DIR__ . '/../../storage/Logs/error.log';

        if (file_exists($logPath)) {
            file_put_contents($logPath, '');
            $output->writeln('<info>Error log cleared successfully!</info>');
        } else {
            $output->writeln('<comment>No error log file found.</comment>');
        }

        return Command::SUCCESS;
    }
}

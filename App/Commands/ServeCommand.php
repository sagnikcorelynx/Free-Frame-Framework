<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ServeCommand extends Command
{
    public function __construct()
    {
        parent::__construct('serve'); // 👈 Set the command name explicitly
    }

    protected function configure()
    {
        $this
            ->setDescription('Serve the application using PHP built-in server')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'Port number to serve on', 8000);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $port = $input->getOption('port') ?? 8000;

        // Define the document root (public folder)
        $documentRoot = __DIR__ . '/../../public';

        $output->writeln("Starting PHP server on http://localhost:$port");

        passthru("php -S localhost:$port -t $documentRoot");

        return Command::SUCCESS;
    }
}

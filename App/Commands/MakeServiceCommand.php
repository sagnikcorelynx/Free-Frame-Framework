<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeServiceCommand extends Command
{
    protected static $defaultName = 'make:service';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Create a new service class')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the service class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $serviceName = ucfirst($name);

        $directory = __DIR__ . '/../Services/';
        $filename = $directory . $serviceName . '.php';

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($filename)) {
            $output->writeln("<error>Service $serviceName already exists!</error>");
            return Command::FAILURE;
        }

        $template = <<<PHP
<?php

namespace App\Services;

class $serviceName
{
    public function __construct()
    {
        // Service constructor
    }
}
PHP;

        file_put_contents($filename, $template);
        $output->writeln("<info>Service $serviceName created successfully!</info>");

        return Command::SUCCESS;
    }
}

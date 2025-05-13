<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeControllerCommand extends Command
{
    protected static $defaultName = 'make:controller';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function configure()
    {
        $this
            ->setName(self::$defaultName) // 👈 explicitly set the name
            ->setDescription('Create a new controller class')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the controller');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $controllerName = ucfirst($name);

        $directory = __DIR__ . '/../Controllers/';
        $filename = $directory . $controllerName . '.php';

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($filename)) {
            $output->writeln("<error>Controller $controllerName already exists!</error>");
            return Command::FAILURE;
        }

        $template = <<<PHP
<?php

namespace App\Controllers;

use App\Controllers\Controller;

class $controllerName extends Controller
{
    public function index()
    {
        // Default index method
    }
}
PHP;

        file_put_contents($filename, $template);
        $output->writeln("<info>Controller $controllerName created successfully!</info>");

        return Command::SUCCESS;
    }
}

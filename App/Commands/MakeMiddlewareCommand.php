<?php

// App/Commands/MakeMiddlewareCommand.php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMiddlewareCommand extends Command
{
    protected static $defaultName = 'make:middleware';
    protected static $defaultDescription = 'Create a new middleware class';

    /**
     * Gets the default name for the command.
     *
     * @return string|null The default name for the command.
     */
    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    /**
     * Configure the command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the middleware');
    }

/**
 * Executes the command to create a new middleware class file.
 *
 * @param InputInterface $input An InputInterface instance to get the command arguments.
 * @param OutputInterface $output An OutputInterface instance to write command output.
 *
 * @return int Command::SUCCESS if the middleware was created successfully, 
 *             or Command::FAILURE if the middleware already exists.
 */

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = trim($input->getArgument('name'));
        $middlewareName = ucfirst($name);
        $dir = __DIR__ . '/../Middleware';
        $filePath = "$dir/{$middlewareName}.php";

        if (!is_dir($dir)) mkdir($dir, 0755, true);
        if (file_exists($filePath)) {
            $output->writeln("<error>Middleware already exists!</error>");
            return Command::FAILURE;
        }

        $template = <<<PHP
<?php

namespace App\Middleware;

use Closure;
use Core\Contracts\MiddlewareInterface;

class {$middlewareName} implements MiddlewareInterface
{
    public function handle(\$request, Closure \$next)
    {
        // Add your logic here

        return \$next(\$request);
    }
}
PHP;

        file_put_contents($filePath, $template);
        $output->writeln("<info>Middleware {$middlewareName} created successfully!</info>");

        return Command::SUCCESS;
    }
}
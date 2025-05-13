<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use File;

class MakeCommandCommand extends Command
{
    protected static $defaultName = 'make:command';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new custom command file')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the command');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Get the command name
        $name = $input->getArgument('name');

        // Determine where to save the new command (App/Commands)
        $path = __DIR__ . '/../Commands/' . $name . 'Command.php';

        // Check if the file already exists
        if (file_exists($path)) {
            $output->writeln("<error>Command file already exists!</error>");
            return Command::FAILURE;
        }

        // Create the file with a basic template
        $template = <<<PHP
<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class {$name}Command extends Command
{
    protected static \$defaultName = '{$name}';

    public static function getDefaultName(): ?string
    {
        return self::\$defaultName;
    }

    protected function configure()
    {
        \$this
            ->setDescription('Custom command for {$name}');
    }

    protected function execute(InputInterface \$input, OutputInterface \$output)
    {
        \$output->writeln('Hello from the {$name} command!');
        return Command::SUCCESS;
    }
}
PHP;

        // Write the template to the file
        file_put_contents($path, $template);

        // Output success message
        $output->writeln("<info>Command file created successfully: {$name}Command.php</info>");

        return Command::SUCCESS;
    }
}

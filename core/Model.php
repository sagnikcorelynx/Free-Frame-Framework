<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class MakeModelCommand extends Command
{
    // ✅ This sets the command name properly
    protected static $defaultName = 'make:model';

    protected function configure()
    {
        $this
            ->setDescription('Create a new model class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the model');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $modelPath = __DIR__ . '/../../App/Models/' . $name . '.php';

        if (file_exists($modelPath)) {
            $output->writeln("<error>Model already exists!</error>");
            return Command::FAILURE;
        }

        $template = "<?php

        namespace App\Models;

        use Core\Model;

        class $name extends Model
        {
            protected \$table = '" . strtolower($name) . "s';
        }
        ";

        file_put_contents($modelPath, $template);

        $output->writeln("<info>Model $name created successfully.</info>");
        return Command::SUCCESS;
    }
}

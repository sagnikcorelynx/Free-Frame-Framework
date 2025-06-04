<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeJobCommand extends Command
{
    protected static $defaultName = 'make:job';

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
        $this
            ->setDescription('Create a new job class')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the job class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $jobName = $input->getArgument('name');

        // Validate job name
        if (!preg_match('/^[A-Z][A-Za-z0-9_]*$/', $jobName)) {
            $output->writeln("<error>Invalid job name. Use CamelCase format (e.g. SendEmailJob).</error>");
            return Command::FAILURE;
        }

        $jobDir = base_path('jobs');
        if (!is_dir($jobDir)) {
            mkdir($jobDir, 0755, true);
        }

        $jobFile = "{$jobDir}/{$jobName}.php";

        if (file_exists($jobFile)) {
            $output->writeln("<error>Job already exists: {$jobFile}</error>");
            return Command::FAILURE;
        }

        $classContent = <<<PHP
<?php

use Core\Jobs\BaseJob;

class {$jobName} extends BaseJob
{
    public function handle()
    {
        // TODO: Implement job logic here
    }
}
PHP;

        file_put_contents($jobFile, $classContent);

        $output->writeln("<info>Job created successfully: jobs/{$jobName}.php</info>");
        return Command::SUCCESS;
    }
}
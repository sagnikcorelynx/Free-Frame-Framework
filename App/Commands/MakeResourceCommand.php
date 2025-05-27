<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeResourceCommand extends Command
{
    protected static $defaultName = 'make:resource';
    protected static $defaultDescription = 'Create a new Resource class';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'The name of the resource class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $className = ucfirst($name);
        $directory = __DIR__ . '/../../app/Resources';
        $filePath = "$directory/$className.php";

        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        if (file_exists($filePath)) {
            $output->writeln("<error>❌ Resource already exists: $filePath</error>");
            return Command::FAILURE;
        }

        $template = <<<PHP
<?php

namespace App\Resources;

use App\Resources\JsonResource;

class $className extends JsonResource
{
    public function toArray(): array
    {
        return [
            
        ];
    }
}
PHP;

        file_put_contents($filePath, $template);

        $output->writeln("<info>✅ Resource created: app/Resources/$className.php</info>");
        return Command::SUCCESS;
    }
}
<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MakeRequestCommand extends Command
{
    protected static $defaultName = 'make:request';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new Request class for validation')
            ->addArgument('name', InputArgument::REQUIRED, 'The name of the request class');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = trim($input->getArgument('name'));
        $className = ucfirst($name);
        $filePath = "App/Requests/{$className}.php";

        if (file_exists($filePath)) {
            $output->writeln("<error>Request class already exists: {$filePath}</error>");
            return Command::FAILURE;
        }

        $template = <<<PHP
<?php

namespace App\Requests;

use Core\Validation\Validator;

class {$className}
{
    protected \$validator;
    protected \$errors = [];

    public function __construct()
    {
        \$this->validator = new Validator();
    }

    public function rules(): array
    {
        return [
            // 'field' => 'required|max:255',
        ];
    }

    public function validate(array \$data): bool
    {
        \$valid = \$this->validator->validate(\$data, \$this->rules());
        \$this->errors = \$this->validator->errors();
        return \$valid;
    }

    public function errors(): array
    {
        return \$this->errors;
    }
}
PHP;

        if (!is_dir('App/Requests')) {
            mkdir('App/Requests', 0777, true);
        }

        file_put_contents($filePath, $template);

        $output->writeln("<info>Request class created successfully: {$filePath}</info>");
        return Command::SUCCESS;
    }
}
<?php

namespace App\Commands;

use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigrationCommand extends Command
{
    protected static $defaultName = 'make:migration';
    protected static $defaultDescription = 'Create a new migration file';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Migration name');
    }

        /**
         * Executes the command.
         *
         * @param InputInterface $input
         * @param OutputInterface $output
         *
         * @return int
         */
    protected function execute(InputInterface $input, OutputInterface $output): int
{
    $name = $input->getArgument('name');
    $className = $this->generateClassName($name);
    $timestamp = (new DateTime())->format('Y_m_d_His');
    $filename = "{$timestamp}_{$name}.php";
    $path = dirname(__DIR__, 2) . "/database/migrations/{$filename}";

    // Detect table name
    $tableName = $this->extractTableName($name);

    // Inject table logic if name indicates "create_xxx_table"
    $upLogic = '';
    $downLogic = '';
    if ($tableName) {
        $upLogic = <<<PHP
        if (!\$schema->tablesExist(['{$tableName}'])) {
            \$table = new \\Doctrine\\DBAL\\Schema\\Table('{$tableName}');
            \$table->addColumn('id', 'integer', ['autoincrement' => true]);
            \$table->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
            \$table->addColumn('updated_at', 'datetime', ['notnull' => false]);
            \$table->addColumn('deleted_at', 'datetime', ['notnull' => false]);
            \$table->setPrimaryKey(['id']);
            \$schema->createTable(\$table);
        }
PHP;

        $downLogic = <<<PHP
        if (\$schema->tablesExist(['{$tableName}'])) {
            \$schema->dropTable('{$tableName}');
        }
PHP;
    }

    $template = <<<PHP
<?php

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Connection;

return new class {
    public function up(Connection \$connection)
    {
        \$schema = \$connection->createSchemaManager();
{$upLogic}
    }

    public function down(Connection \$connection)
    {
        \$schema = \$connection->createSchemaManager();
{$downLogic}
    }
};
PHP;

    if (!is_dir(dirname($path))) {
        mkdir(dirname($path), 0777, true);
    }

    file_put_contents($path, $template);

    $output->writeln("✅ Migration created: database/migrations/{$filename}");

    return Command::SUCCESS;
}

    private function generateClassName(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $name)));
    }

    private function extractTableName(string $name): ?string
    {
        // Match patterns like "create_users_table" or "drop_orders_table"
        if (preg_match('/^(create|drop)_(\w+)_table$/', $name, $matches)) {
            return $matches[2]; // returns "users" or "orders"
        }
        return null;
    }
}
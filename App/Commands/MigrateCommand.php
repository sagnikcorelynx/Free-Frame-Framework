<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PDO;

class MigrateCommand extends Command
{
    protected static $defaultName = 'migrate';

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
 * Configures the command by setting its description.
 * This description is displayed when listing available commands.
 */

    protected function configure()
    {
        $this->setDescription('Run the database migrations');
    }

    /**
     * Executes the command.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return int 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pdo = $this->getPDO();

        $this->createMigrationsTable($pdo);

        $migrated = $this->getMigratedMigrations($pdo);
        $files = glob(__DIR__ . '/../../database/migrations/*.php');

        $batch = time();
        $ran = 0;

        foreach ($files as $file) {
            $className = $this->getClassName($file);

            if (in_array($className, $migrated)) {
                continue;
            }

            require_once $file;
            $migration = new $className;

            $query = $migration->up();
            $pdo->exec($query);

            $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)")
                ->execute([$className, $batch]);

            $output->writeln("<info>Migrated:</info> $className");
            $ran++;
        }

        if ($ran === 0) {
            $output->writeln("<comment>No new migrations.</comment>");
        }

        return Command::SUCCESS;
    }

    private function getPDO(): PDO
    {
        $db = require __DIR__ . '/../../config/database.php';
        return new PDO("mysql:host={$db['host']};dbname={$db['database']}", $db['username'], $db['password']);
    }

    private function createMigrationsTable(PDO $pdo)
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255),
                batch INT,
                migrated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    private function getMigratedMigrations(PDO $pdo)
    {
        $stmt = $pdo->query("SELECT migration FROM migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    private function getClassName($file)
    {
        $contents = file_get_contents($file);
        preg_match('/class\s+([^\s]+)/', $contents, $matches);
        return $matches[1];
    }
}

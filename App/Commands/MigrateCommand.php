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

    /**
     * Get the PDO instance.
     *
     * @return PDO
     */
    private function getPDO(): PDO
    {
        $driver = config('database.default', 'mysql');
        $db = config("database.connections.$driver");
        if (!$db) {
            throw new RuntimeException("Database connection [$driver] not configured.");
        }
        $dsn = "{$db['driver']}:host={$db['host']};dbname={$db['database']};charset=utf8mb4";
        return new PDO(
            $dsn,
            $db['username'],
            $db['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
    }

    /**
     * Creates the migrations table if it does not exist.
     *
     * @param PDO $pdo
     * @return void
     */
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

    /**
     * Get all the migration files that have already been run.
     *
     * @param PDO $pdo
     * @return array
     */
    private function getMigratedMigrations(PDO $pdo)
    {
        $stmt = $pdo->query("SELECT migration FROM migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get the class name from a file name.
     *
     * @param string $file
     * @return string
     */
    private function getClassName($file)
    {
        $contents = file_get_contents($file);
        preg_match('/class\s+([^\s]+)/', $contents, $matches);
        return $matches[1];
    }
}

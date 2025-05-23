<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PackageListCommand extends Command
{
    protected static $defaultName = 'package:list';
    protected static $defaultDescription = 'Show list of installed Composer packages';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }
    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $installedFile = realpath(__DIR__ . '/../../vendor/composer/installed.json');
        if (!file_exists($installedFile)) {
            $output->writeln("<error>installed.json not found. Please run 'composer install' first.</error>");
            return Command::FAILURE;
        }

        $data = json_decode(file_get_contents($installedFile), true);

        // Composer 2+ uses a "packages" key
        $packages = $data['packages'] ?? $data;

        $output->writeln("<info>Installed Packages:</info>");
        $output->writeln(str_repeat('-', 40));
        foreach ($packages as $package) {
            $name = $package['name'] ?? 'unknown';
            $version = $package['version'] ?? 'unknown';
            $output->writeln("  $name  =>  $version");
        }
        $output->writeln(str_repeat('-', 40));

        return Command::SUCCESS;
    }
}
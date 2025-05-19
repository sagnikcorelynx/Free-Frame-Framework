<?php

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StorageLinkCommand extends Command
{
    protected static $defaultName = 'storage:link';
    protected static $defaultDescription = 'Create a symbolic link from "public/storage" to "storage/Public".';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $publicPath = realpath(__DIR__ . '/../../public');
        $storagePublicPath = realpath(__DIR__ . '/../../storage/Public');
        $link = $publicPath . '/storage';

        if (!$storagePublicPath) {
            $output->writeln('<error>storage/Public directory does not exist.</error>');
            return Command::FAILURE;
        }

        if (is_link($link) || file_exists($link)) {
            $output->writeln('<comment>Link already exists at public/storage.</comment>');
            return Command::SUCCESS;
        }

        if (symlink($storagePublicPath, $link)) {
            $output->writeln('<info>Symbolic link created: public/storage → storage/Public</info>');
        } else {
            $output->writeln('<error>Failed to create the symbolic link.</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
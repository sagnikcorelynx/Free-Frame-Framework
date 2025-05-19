<?php

namespace App\Commands;

use Core\Router;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RouteListCommand extends Command
{
    protected static $defaultName = 'route:list';
    protected static $defaultDescription = 'List all registered routes.';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $router = require __DIR__ . '/../../routes/route.php';
        $routes = $router->getRoutes();

        if (empty($routes)) {
            $output->writeln("<comment>No routes found.</comment>");
            return Command::SUCCESS;
        }

        $output->writeln("<info>Available Routes:</info>");
        $output->writeln("METHOD\tURI\t\tACTION");

        foreach ($routes as $route) {
            $method = $route['method'] ?? 'N/A';
            $uri = $route['uri'] ?? 'N/A';
            $action = is_string($route['action']) ? $route['action'] : 'Closure';

            $output->writeln("{$method}\t{$uri}\t\t{$action}");
        }

        return Command::SUCCESS;
    }
}
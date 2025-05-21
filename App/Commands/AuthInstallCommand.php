<?php

namespace App\Commands;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;

class AuthInstallCommand extends Command
{
    protected static $defaultName = 'auth:install';

    public static function getDefaultName(): ?string
    {
        return self::$defaultName;
    }
    protected function configure()
    {
        $this->setDescription('Install JWT-based Authentication scaffolding');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $controllerPath = __DIR__ . '/../Controllers/AuthController.php';
        $middlewarePath = __DIR__ . '/../Middleware/JwtMiddleware.php';

        // Create AuthController
        if (!file_exists(dirname($controllerPath))) {
            mkdir(dirname($controllerPath), 0755, true);
        }

        file_put_contents($controllerPath, $this->getAuthControllerStub());
        $output->writeln("<info>AuthController created.</info>");

        // Create JwtMiddleware
        if (!file_exists(dirname($middlewarePath))) {
            mkdir(dirname($middlewarePath), 0755, true);
        }

        file_put_contents($middlewarePath, $this->getJwtMiddlewareStub());
        $output->writeln("<info>JwtMiddleware created.</info>");

        // Append JWT_SECRET to .env
        $envPath = __DIR__ . '/../../.env';
        $secret = bin2hex(random_bytes(32));

        if (strpos(file_get_contents($envPath), 'JWT_SECRET=') === false) {
            file_put_contents($envPath, "\nJWT_SECRET=$secret", FILE_APPEND);
            $output->writeln("<info>JWT_SECRET added to .env</info>");
        } else {
            $output->writeln("<comment>JWT_SECRET already exists in .env</comment>");
        }

        $output->writeln("<info>JWT Auth scaffolding installed successfully.</info>");
        return Command::SUCCESS;
    }

    protected function getAuthControllerStub(): string
    {
        return <<<PHP
<?php

namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class AuthController
{
    public function register()
    {
        
    }

    public function login()
    {
        
    }
}
PHP;
    }

    protected function getJwtMiddlewareStub(): string
    {
        return <<<PHP
<?php

namespace App\Http\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtMiddleware
{
    public static function handle(\$request, \$next)
    {
        \$headers = getallheaders();

        if (!isset(\$headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['message' => 'Authorization header missing']);
            exit;
        }

        \$token = str_replace('Bearer ', '', \$headers['Authorization']);

        try {
            \$decoded = JWT::decode(\$token, new Key(env('JWT_SECRET'), 'HS256'));
            \$request['user_id'] = \$decoded->sub;
            return \$next(\$request);
        } catch (\Exception \$e) {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid token']);
            exit;
        }
    }
}
PHP;
    }
}

<?php

declare(strict_types=1);

namespace Sasd\LogSink;

final class Bootstrap
{
    public static function run(string $projectRoot): void
    {
        self::registerAutoloader();

        $config = Config::fromEnvFile($projectRoot . '/.env');
        $logger = new ServiceLogger($projectRoot, $config);
        $database = new Database($config);
        $repository = new LogRepository($database);
        $app = new App($repository, $logger, $config);

        $app->handle();
    }

    private static function registerAutoloader(): void
    {
        spl_autoload_register(static function (string $className): void {
            $prefix = 'Sasd\\LogSink\\';

            if (!str_starts_with($className, $prefix)) {
                return;
            }

            $relativeClass = substr($className, strlen($prefix));
            $file = __DIR__ . '/' . str_replace('\\', '/', $relativeClass) . '.php';

            if (is_file($file)) {
                require $file;
            }
        });
    }
}

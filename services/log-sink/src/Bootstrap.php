<?php

declare(strict_types=1);

namespace Sasd\LogSink;

final class Bootstrap
{
    public static function run(string $projectRoot): void
    {
        self::registerAutoloader();

        $config = Config::fromEnvFile(self::resolveEnvFile($projectRoot));

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

    private static function resolveEnvFile(string $projectRoot): string
    {
        $explicitEnvFile = getenv('LOGSINK_ENV_FILE');

        if (is_string($explicitEnvFile) && $explicitEnvFile !== '') {
            return $explicitEnvFile;
        }

        $candidates = [];

        /*
         * IONOS-Beispiel:
         *
         * projectRoot:
         *   /homepages/.../htdocs/de.sasd/api/logsink
         *
         * gewünschte Datei:
         *   /homepages/.../htdocs/de.sasd/.env-logsink
         *
         * Das ist zwei Ebenen über dem Service-Verzeichnis.
         */
        $candidates[] = dirname(dirname($projectRoot)) . '/.env-logsink';

        /*
         * Alternative: eine Ebene über dem Service-Verzeichnis.
         *
         * Beispiel:
         *   /homepages/.../htdocs/de.sasd/api/.env-logsink
         */
        $candidates[] = dirname($projectRoot) . '/.env-logsink';

        /*
         * Lokale Entwicklungsumgebung:
         *
         *   services/log-sink/.env
         */
        $candidates[] = $projectRoot . '/.env';

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return $projectRoot . '/.env';
    }
}
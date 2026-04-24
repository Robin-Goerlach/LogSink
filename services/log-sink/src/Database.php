<?php

declare(strict_types=1);

namespace Sasd\LogSink;

use PDO;

final class Database
{
    private ?PDO $pdo = null;

    public function __construct(
        private readonly Config $config
    ) {
    }

    public function pdo(): PDO
    {
        if ($this->pdo instanceof PDO) {
            return $this->pdo;
        }

        $host = $this->config->string('DB_HOST', '127.0.0.1');
        $port = $this->config->int('DB_PORT', 3306);
        $database = $this->config->string('DB_DATABASE', 'sasd_logging');
        $username = $this->config->string('DB_USERNAME', 'logging_service');
        $password = $this->config->string('DB_PASSWORD', '');

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $host,
            $port,
            $database
        );

        $this->pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $this->pdo;
    }
}

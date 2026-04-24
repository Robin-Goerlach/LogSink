<?php

declare(strict_types=1);

namespace Sasd\LogSink;

use Throwable;

final class ServiceLogger
{
    public function __construct(
        private readonly string $projectRoot,
        private readonly Config $config
    ) {
    }

    public function info(string $message): void
    {
        $this->write('INFO', $message);
    }

    public function error(string $message, ?Throwable $exception = null): void
    {
        if ($exception !== null) {
            $message .= ' | ' . $exception::class . ': ' . $exception->getMessage();
        }

        $this->write('ERROR', $message);
    }

    private function write(string $level, string $message): void
    {
        if (!$this->config->bool('SERVICE_LOG_ENABLED', true)) {
            return;
        }

        $relativeFile = $this->config->string('SERVICE_LOG_FILE', 'var/log/service.log');
        $file = $this->projectRoot . '/' . ltrim($relativeFile, '/');

        $directory = dirname($file);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $line = sprintf(
            "[%s] %-5s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $message
        );

        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}

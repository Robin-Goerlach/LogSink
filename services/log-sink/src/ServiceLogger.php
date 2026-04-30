<?php

declare(strict_types=1);

namespace Sasd\LogSink;

use Throwable;

/**
 * Sehr einfacher Service-Logger für die aktuelle V1.
 *
 * Aufgabe:
 * --------
 * Diese Klasse schreibt technische Service-Meldungen in eine lokale Logdatei.
 *
 * LS-021:
 * -------
 * Die Methoden können jetzt eine Request-ID entgegennehmen. Dadurch lassen
 * sich mehrere technische Logzeilen derselben HTTP-Anfrage zuordnen.
 */
final class ServiceLogger
{
    public function __construct(
        private readonly string $projectRoot,
        private readonly Config $config
    ) {
    }

    public function info(string $message, ?string $requestId = null): void
    {
        $this->write('INFO', $message, $requestId);
    }

    public function error(
        string $message,
        ?Throwable $exception = null,
        ?string $requestId = null
    ): void {
        if ($exception !== null) {
            $message .= ' | ' . $exception::class . ': ' . $exception->getMessage();
        }

        $this->write('ERROR', $message, $requestId);
    }

    /**
     * Schreibt eine Zeile in die Service-Logdatei.
     *
     * Die Datei wird über SERVICE_LOG_FILE konfiguriert.
     *
     * Standard:
     *
     *   var/log/service.log
     *
     * Der Pfad ist relativ zum Projektroot. Dadurch funktioniert er lokal und
     * bei IONOS ähnlich.
     */
    private function write(string $level, string $message, ?string $requestId = null): void
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

        $requestIdPart = '';

        if (is_string($requestId) && $requestId !== '') {
            $requestIdPart = ' requestId=' . $requestId;
        }

        $line = sprintf(
            "[%s] %-5s%s %s\n",
            date('Y-m-d H:i:s'),
            $level,
            $requestIdPart,
            $message
        );

        /*
         * LOCK_EX verhindert, dass zwei parallele Schreibvorgänge die Datei
         * leicht beschädigen. Für V1 reicht das aus. Für hohe Last wäre ein
         * robusteres Logging-Konzept sinnvoll.
         */
        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}

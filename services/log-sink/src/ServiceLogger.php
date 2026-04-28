<?php

declare(strict_types=1);

namespace Sasd\LogSink;

use Throwable;

/**
 * Einfacher technischer Logger für den Service selbst.
 *
 * Wichtig:
 * --------
 * Dieser Logger ist nicht der LogSink-Fachzweck.
 *
 * LogSink nimmt Logmeldungen von Clients entgegen und speichert sie in der
 * Datenbank. Das ist die fachliche Logging-Funktion.
 *
 * ServiceLogger dagegen schreibt interne technische Meldungen des PHP-Services,
 * z. B.:
 *
 * - ein Logeintrag wurde gespeichert,
 * - eine Exception ist aufgetreten,
 * - später vielleicht Start-/Diagnoseinformationen.
 *
 * Ziel:
 * -----
 * Wenn der Service selbst Probleme hat, kann man in var/log/service.log
 * nachsehen.
 */
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

        /*
         * LOCK_EX verhindert, dass zwei parallele Schreibvorgänge die Datei
         * leicht beschädigen. Für V1 reicht das aus. Für hohe Last wäre ein
         * robusteres Logging-Konzept sinnvoll.
         */
        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }
}

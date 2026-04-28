<?php

declare(strict_types=1);

namespace Sasd\LogSink;

/**
 * Startet und verdrahtet den LogSink-Service.
 *
 * Diese Klasse ist der zentrale Bootstrapping-Punkt der aktuellen V1.
 *
 * Bootstrapping bedeutet hier:
 *
 * - Klassen automatisch laden
 * - Konfiguration suchen und lesen
 * - Logger erzeugen
 * - Datenbankzugriff vorbereiten
 * - Repository erzeugen
 * - App erzeugen und HTTP-Request verarbeiten
 *
 * In größeren Frameworks wie Symfony oder Laravel wird ein großer Teil dieser
 * Arbeit vom Framework erledigt. LogSink macht das in V1 bewusst selbst, damit
 * der Ablauf sichtbar und für Lernzwecke nachvollziehbar bleibt.
 */
final class Bootstrap
{
    /**
     * Startet die Anwendung.
     *
     * @param string $projectRoot Absoluter Pfad zum Service-Root.
     *                            Beispiel lokal: services/log-sink
     *                            Beispiel IONOS: /homepages/.../htdocs/de.sasd/api/logsink
     */
    public static function run(string $projectRoot): void
    {
        /*
         * Ohne Composer brauchen wir einen kleinen eigenen Autoloader.
         *
         * Der Autoloader sorgt dafür, dass Klassen wie Config, Database oder App
         * automatisch aus dem src/-Verzeichnis geladen werden, sobald PHP sie
         * zum ersten Mal benötigt.
         */
        self::registerAutoloader();

        /*
         * Konfiguration laden.
         *
         * Wichtig:
         * Früher wurde nur $projectRoot . '/.env' gelesen.
         *
         * Für IONOS wurde die echte Konfiguration aber aus dem öffentlich
         * erreichbaren Service-Verzeichnis heraus verschoben. Deshalb wird jetzt
         * resolveEnvFile() verwendet.
         */
        $config = Config::fromEnvFile(self::resolveEnvFile($projectRoot));

        /*
         * Der ServiceLogger schreibt technische Meldungen des Services selbst.
         *
         * Das ist nicht das gleiche wie die Logmeldungen, die Clients an LogSink
         * senden. Client-Logmeldungen landen in der Datenbank. Service-Logs
         * landen aktuell in var/log/service.log.
         */
        $logger = new ServiceLogger($projectRoot, $config);

        /*
         * Database kapselt die PDO-Verbindung zur MariaDB.
         *
         * Die Verbindung wird lazy aufgebaut: Erst wenn ein Repository wirklich
         * pdo() aufruft, wird die Verbindung hergestellt.
         */
        $database = new Database($config);

        /*
         * Repository-Schicht:
         *
         * LogRepository kennt die SQL-Statements. Dadurch muss App.php nicht
         * selbst SQL enthalten.
         */
        $repository = new LogRepository($database);

        /*
         * App verarbeitet den eigentlichen HTTP-Request.
         *
         * In der aktuellen V1 bedeutet das:
         *
         * - POST => Rohmeldung speichern
         * - GET  => letzte Meldungen lesen
         */
        $app = new App($repository, $logger, $config);

        /*
         * Ab hier übernimmt App die Kontrolle über den aktuellen Request.
         */
        $app->handle();
    }

    /**
     * Registriert einen sehr einfachen PSR-4-ähnlichen Autoloader.
     *
     * Der Namespace Sasd\LogSink\ wird auf das aktuelle src/-Verzeichnis
     * abgebildet.
     *
     * Beispiel:
     *
     *   Sasd\LogSink\Config
     *
     * wird geladen aus:
     *
     *   src/Config.php
     *
     * Später, wenn Composer eingeführt wird, ersetzt Composer diesen eigenen
     * Autoloader.
     */
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

    /**
     * Sucht die passende Konfigurationsdatei.
     *
     * Warum nicht einfach immer .env im Projektordner?
     * ------------------------------------------------
     * Bei einem Webhosting kann eine .env-Datei im Service-Verzeichnis
     * versehentlich per Browser erreichbar sein. Genau das ist beim IONOS-Test
     * sichtbar geworden.
     *
     * Deshalb unterstützt LogSink jetzt eine externe Datei:
     *
     *   .env-logsink
     *
     * Diese Datei liegt außerhalb des Service-Verzeichnisses und ist dadurch
     * nicht direkt über /logsink/.env erreichbar.
     *
     * Suchreihenfolge:
     *
     * 1. LOGSINK_ENV_FILE, falls als echte Umgebungsvariable gesetzt.
     * 2. Zwei Ebenen über dem Service-Verzeichnis: für IONOS/de.sasd.
     * 3. Eine Ebene über dem Service-Verzeichnis: alternative Hosting-Struktur.
     * 4. Lokale Entwicklungsdatei im Projektroot: .env.
     *
     * Wenn nichts gefunden wird, wird am Ende trotzdem $projectRoot . '/.env'
     * zurückgegeben. Config::fromEnvFile() behandelt eine fehlende Datei
     * defensiv und liefert dann eine leere Konfiguration mit Defaultwerten.
     */
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

<?php

declare(strict_types=1);

namespace Sasd\LogSink;

use PDO;

/**
 * Kapselt die PDO-Verbindung zur MariaDB.
 *
 * PDO ist die Datenbankabstraktion von PHP. Für LogSink verwenden wir sie direkt,
 * weil das V1-Projekt bewusst klein bleiben soll und noch kein ORM oder
 * Framework eingeführt wird.
 *
 * Diese Klasse hat genau eine Aufgabe:
 *
 *   Aus der Konfiguration eine funktionierende PDO-Verbindung bauen.
 *
 * Sie kennt keine HTTP-Requests und keine Logik darüber, welche Logs gespeichert
 * oder gelesen werden. Diese Verantwortung liegt beim Repository.
 */
final class Database
{
    private ?PDO $pdo = null;

    public function __construct(
        private readonly Config $config
    ) {
    }

    /**
     * Liefert die PDO-Verbindung.
     *
     * Lazy Initialization:
     * --------------------
     * Die Verbindung wird erst aufgebaut, wenn sie wirklich gebraucht wird.
     * Danach wird dieselbe Verbindung für weitere Aufrufe wiederverwendet.
     */
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

        /*
         * DSN = Data Source Name.
         *
         * charset=utf8mb4 ist wichtig, damit Unicode-Zeichen sauber gespeichert
         * werden können, z. B. Umlaute, Emojis oder internationale Texte.
         */
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $host,
            $port,
            $database
        );

        $this->pdo = new PDO($dsn, $username, $password, [
            /*
             * Datenbankfehler werden als Exceptions geworfen.
             * Das ist für Lernzwecke und Fehlerbehandlung deutlich besser als
             * stille Fehlercodes.
             */
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

            /*
             * SELECT-Ergebnisse werden standardmäßig als assoziative Arrays
             * geliefert.
             */
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

            /*
             * Prepared Statements sollen vom MySQL/MariaDB-Treiber verarbeitet
             * werden und nicht von PDO emuliert werden.
             */
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return $this->pdo;
    }
}

<?php

declare(strict_types=1);

namespace Sasd\LogSink;

/**
 * Datenbankzugriff für Logmeldungen.
 *
 * Repository bedeutet hier:
 *
 * - App.php muss kein SQL kennen.
 * - SQL steht an einer zentralen Stelle.
 * - Schreiben und Lesen der Logs sind klar getrennt.
 *
 * Die aktuelle V1 ist bewusst einfach:
 *
 * - insertRawLog(): speichert den kompletten Request-Body unverändert.
 * - findLatest(): liest die letzten Logmeldungen aus der View v_log_entries.
 *
 * Später wird es vermutlich weitere Repositories geben, z. B. für strukturierte
 * Events, Sources, Credentials, Scopes und Audit.
 */
final class LogRepository
{
    public function __construct(
        private readonly Database $database
    ) {
    }

    /**
     * Speichert eine rohe Logmeldung.
     *
     * Wichtig:
     * --------
     * Die V1 validiert den Body noch nicht. Der komplette Request-Body wird so
     * gespeichert, wie er angekommen ist.
     *
     * Das ist bewusst gewählt, damit der erste Prototyp sehr einfach bleibt.
     * Später wird ein strukturierter Ingest-Endpunkt ergänzt.
     *
     * @param string $rawBody kompletter HTTP-Request-Body
     * @param string $sourceIp erkannte Client-IP
     * @param int|null $sourcePort erkannter Client-Port, falls verfügbar
     * @param string $httpMethod HTTP-Methode, aktuell meist POST
     * @param string $requestUri angefragte URI
     * @param string $contentType Content-Type des Requests
     * @param string $userAgent User-Agent des Clients
     * @param array<string, string> $headers HTTP-Header als Name/Wert-Paare
     *
     * @return array<string, mixed> technische Informationen zur gespeicherten Logmeldung
     */
    public function insertRawLog(
        string $rawBody,
        string $sourceIp,
        ?int $sourcePort,
        string $httpMethod,
        string $requestUri,
        string $contentType,
        string $userAgent,
        array $headers
    ): array {
        $pdo = $this->database->pdo();

        /*
         * Die Header werden als JSON gespeichert.
         *
         * Für die V1 ist das ausreichend. Später sollte geprüft werden, welche
         * Header aus Sicherheitsgründen gar nicht gespeichert werden dürfen
         * (z. B. Authorization).
         */
        $headersJson = json_encode(
            $headers,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        /*
         * Prepared Statement:
         *
         * Werte werden nicht per Stringverkettung in SQL eingebaut. Das schützt
         * vor SQL-Injection und ist eine grundlegende Sicherheitsregel.
         */
        $statement = $pdo->prepare(
            'INSERT INTO log_entries (
                raw_message,
                source_ip,
                source_port,
                http_method,
                request_uri,
                content_type,
                user_agent,
                request_headers_json
            ) VALUES (
                :raw_message,
                :source_ip,
                :source_port,
                :http_method,
                :request_uri,
                :content_type,
                :user_agent,
                :request_headers_json
            )'
        );

        $statement->bindValue(':raw_message', $rawBody);
        $statement->bindValue(':source_ip', $sourceIp !== '' ? $sourceIp : null);
        $statement->bindValue(':source_port', $sourcePort);
        $statement->bindValue(':http_method', $httpMethod !== '' ? $httpMethod : null);
        $statement->bindValue(':request_uri', $requestUri !== '' ? $requestUri : null);
        $statement->bindValue(':content_type', $contentType !== '' ? $contentType : null);
        $statement->bindValue(':user_agent', $userAgent !== '' ? $userAgent : null);
        $statement->bindValue(':request_headers_json', $headersJson !== false ? $headersJson : null);

        $statement->execute();

        /*
         * lastInsertId() liefert die gerade erzeugte ID.
         *
         * Danach lesen wir einige vom Datenbanktrigger gesetzte Werte zurück,
         * z. B. Größe und SHA-256-Hash.
         */
        $id = (int) $pdo->lastInsertId();

        $resultStatement = $pdo->prepare(
            'SELECT
                id,
                received_at,
                raw_message_size,
                payload_sha256
            FROM log_entries
            WHERE id = :id'
        );

        $resultStatement->execute(['id' => $id]);

        $row = $resultStatement->fetch();

        return is_array($row) ? $row : ['id' => $id];
    }

    /**
     * Liest die letzten Logmeldungen.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findLatest(int $limit = 100): array
    {
        /*
         * Sicherheits- und Stabilitätsgrenze:
         *
         * Auch wenn der Client limit=999999 sendet, werden höchstens 1000
         * Einträge geliefert.
         */
        $limit = max(1, min(1000, $limit));

        /*
         * Die View v_log_entries bereitet die Rohdaten für die API auf.
         *
         * Vorteil:
         * Der PHP-Code muss nicht selbst entscheiden, wie raw_message_text oder
         * raw_message_base64 erzeugt werden.
         */
        $statement = $this->database->pdo()->prepare(
            'SELECT
                id,
                received_at,
                source_ip,
                source_port,
                http_method,
                request_uri,
                content_type,
                user_agent,
                raw_message_size,
                payload_sha256,
                raw_message_text,
                raw_message_base64
            FROM v_log_entries
            ORDER BY id DESC
            LIMIT :limit'
        );

        $statement->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll();
    }
}

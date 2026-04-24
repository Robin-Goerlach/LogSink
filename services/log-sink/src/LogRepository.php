<?php

declare(strict_types=1);

namespace Sasd\LogSink;

final class LogRepository
{
    public function __construct(
        private readonly Database $database
    ) {
    }

    /**
     * @param array<string, string> $headers
     * @return array<string, mixed>
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

        $headersJson = json_encode(
            $headers,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        $statement = $pdo->prepare(
            'INSERT INTO log_entries
                (
                    raw_message,
                    source_ip,
                    source_port,
                    http_method,
                    request_uri,
                    content_type,
                    user_agent,
                    request_headers_json
                )
             VALUES
                (
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
     * @return array<int, array<string, mixed>>
     */
    public function findLatest(int $limit = 100): array
    {
        $limit = max(1, min(1000, $limit));

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

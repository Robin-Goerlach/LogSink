<?php

declare(strict_types=1);

namespace Sasd\LogSink;

use Throwable;

/**
 * Verarbeitet den aktuellen HTTP-Request.
 *
 * Diese Klasse ist in der V1 absichtlich sehr einfach.
 *
 * Aktueller Ablauf:
 *
 * - POST => Request-Body unverändert als Logmeldung speichern
 * - GET  => letzte Logmeldungen lesen
 * - andere Methoden => 405 Method Not Allowed
 *
 * LS-021:
 * -------
 * Ab diesem Schritt bekommt jeder Request eine serverseitige Request-ID.
 * Diese ID erscheint in:
 *
 * - HTTP-Header `X-Request-ID`
 * - JSON-Antwortfeld `requestId`
 * - Service-Logdatei
 *
 * Die Request-ID ist keine Authentifizierung. Sie ist ein Aktenzeichen für
 * genau einen HTTP-Request und hilft beim Debugging, bei Supportfällen und
 * später beim Audit-Logging.
 */
final class App
{
    private readonly string $requestId;

    public function __construct(
        private readonly LogRepository $repository,
        private readonly ServiceLogger $logger,
        private readonly Config $config
    ) {
        /*
         * Für LS-021 erzeugt der Server die Request-ID immer selbst.
         *
         * Das ist absichtlich einfacher und sicherer als eine vom Client
         * gelieferte X-Request-ID ungeprüft zu übernehmen.
         */
        $this->requestId = self::generateRequestId();
    }

    /**
     * Einstiegspunkt für den aktuellen Request.
     *
     * Diese Methode entscheidet anhand der HTTP-Methode, was passieren soll.
     */
    public function handle(): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

            $this->logger->info(
                'Request handling started: method=' . $method,
                $this->requestId
            );

            if ($method === 'POST') {
                $this->writeLog();
                return;
            }

            if ($method === 'GET') {
                $this->readLogs();
                return;
            }

            $this->logger->info(
                'Request rejected: method not allowed: method=' . $method,
                $this->requestId
            );

            $this->json([
                'status' => 'error',
                'error' => 'method_not_allowed',
                'message' => 'Use POST to write logs or GET to read latest logs.',
            ], 405);
        } catch (Throwable $exception) {
            /*
             * Wichtig für später:
             *
             * Im Entwicklungsmodus darf die Fehlermeldung sichtbar sein.
             * Im produktionsnahen Betrieb sollte APP_DEBUG=false sein, damit
             * keine internen Details an Clients ausgeliefert werden.
             *
             * Die Request-ID erlaubt trotzdem eine gezielte Suche im Service-Log.
             */
            $this->logger->error(
                'Unhandled service error',
                $exception,
                $this->requestId
            );

            $this->json([
                'status' => 'error',
                'error' => 'internal_server_error',
                'message' => $this->config->bool('APP_DEBUG', false)
                    ? $exception->getMessage()
                    : 'Internal server error.',
            ], 500);
        }
    }

    /**
     * Speichert eine Logmeldung.
     *
     * V1-Prinzip:
     * -----------
     * Der Body wird unverändert gespeichert. Der Service interessiert sich noch
     * nicht dafür, ob der Body JSON, Text oder etwas anderes ist.
     */
    private function writeLog(): void
    {
        /*
         * php://input ist der rohe HTTP-Request-Body.
         *
         * Bei POST ist das genau das, was der Client gesendet hat.
         */
        $rawBody = file_get_contents('php://input');

        if ($rawBody === false) {
            $rawBody = '';
        }

        $result = $this->repository->insertRawLog(
            rawBody: $rawBody,
            sourceIp: $_SERVER['REMOTE_ADDR'] ?? '',
            sourcePort: isset($_SERVER['REMOTE_PORT']) ? (int) $_SERVER['REMOTE_PORT'] : null,
            httpMethod: $_SERVER['REQUEST_METHOD'] ?? '',
            requestUri: $_SERVER['REQUEST_URI'] ?? '',
            contentType: $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? ''),
            userAgent: $_SERVER['HTTP_USER_AGENT'] ?? '',
            headers: $this->headers()
        );

        $this->logger->info(
            'Log entry written with id ' . ($result['id'] ?? 'unknown'),
            $this->requestId
        );

        $this->json([
            'status' => 'created',
            'id' => isset($result['id']) ? (int) $result['id'] : null,
            'receivedAt' => $result['received_at'] ?? null,
            'rawMessageSize' => isset($result['raw_message_size']) ? (int) $result['raw_message_size'] : null,
            'payloadSha256' => $result['payload_sha256'] ?? null,
        ], 201);
    }

    /**
     * Liest Logmeldungen.
     *
     * Der Client kann über ?limit=... steuern, wie viele Meldungen er haben
     * möchte. Das Repository begrenzt den Wert zusätzlich auf maximal 1000.
     */
    private function readLogs(): void
    {
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 100;

        $this->logger->info(
            'Latest log entries requested: limit=' . $limit,
            $this->requestId
        );

        $this->json([
            'status' => 'ok',
            'items' => $this->repository->findLatest($limit),
        ]);
    }

    /**
     * Sammelt die HTTP-Header des aktuellen Requests.
     *
     * @return array<string,string>
     */
    private function headers(): array
    {
        /*
         * getallheaders() existiert nicht in jeder PHP-Laufzeitumgebung.
         * Apache stellt es meist bereit, aber für Portabilität gibt es darunter
         * einen Fallback über $_SERVER.
         */
        if (function_exists('getallheaders')) {
            $headers = getallheaders();

            if (is_array($headers)) {
                return array_map('strval', $headers);
            }
        }

        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (!str_starts_with($key, 'HTTP_')) {
                continue;
            }

            /*
             * Aus HTTP_USER_AGENT wird User-Agent.
             */
            $name = substr($key, 5);
            $name = str_replace('_', ' ', strtolower($name));
            $name = str_replace(' ', '-', ucwords($name));

            $headers[$name] = (string) $value;
        }

        return $headers;
    }

    /**
     * Sendet eine JSON-Antwort.
     *
     * LS-021 ergänzt automatisch:
     *
     * - Header `X-Request-ID`
     * - JSON-Feld `requestId`
     *
     * @param array<string,mixed> $data
     */
    private function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);

        header('Content-Type: application/json; charset=utf-8');
        header('X-Request-ID: ' . $this->requestId);

        $data = $this->addRequestIdToResponse($data);

        echo json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Fügt die Request-ID in die JSON-Antwort ein.
     *
     * Wenn `status` vorhanden ist, bleibt `status` absichtlich das erste Feld.
     * Dadurch bleiben die Antworten für Menschen gut lesbar:
     *
     * {
     *   "status": "ok",
     *   "requestId": "req_..."
     * }
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function addRequestIdToResponse(array $data): array
    {
        if (array_key_exists('requestId', $data)) {
            return $data;
        }

        if (array_key_exists('status', $data)) {
            $result = [
                'status' => $data['status'],
                'requestId' => $this->requestId,
            ];

            unset($data['status']);

            return $result + $data;
        }

        return ['requestId' => $this->requestId] + $data;
    }

    /**
     * Erzeugt eine serverseitige Request-ID.
     *
     * Format:
     *
     *   req_ + 32 hexadezimale Zufallszeichen
     *
     * Beispiel:
     *
     *   req_0f6d4c0f7a7c4980a1c02ef8a0d0c4ff
     */
    private static function generateRequestId(): string
    {
        try {
            return 'req_' . bin2hex(random_bytes(16));
        } catch (Throwable) {
            /*
             * random_bytes() sollte normalerweise verfügbar sein.
             * Der Fallback ist nur eine Notbremse, damit der Service im
             * Ausnahmefall trotzdem eine nachvollziehbare ID erzeugt.
             */
            return 'req_' . str_replace('.', '', uniqid('', true));
        }
    }
}

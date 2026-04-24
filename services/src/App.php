<?php

declare(strict_types=1);

namespace Sasd\LogSink;

use Throwable;

final class App
{
    public function __construct(
        private readonly LogRepository $repository,
        private readonly ServiceLogger $logger
    ) {
    }

    public function handle(): void
    {
        try {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

            if ($method === 'POST') {
                $this->writeLog();
                return;
            }

            if ($method === 'GET') {
                $this->readLogs();
                return;
            }

            $this->json([
                'status' => 'error',
                'error' => 'method_not_allowed',
                'message' => 'Use POST to write logs or GET to read latest logs.',
            ], 405);
        } catch (Throwable $exception) {
            $this->logger->error('Unhandled service error', $exception);

            $this->json([
                'status' => 'error',
                'error' => 'internal_server_error',
                'message' => $this->debugEnabled() ? $exception->getMessage() : 'Internal server error.',
            ], 500);
        }
    }

    private function writeLog(): void
    {
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

        $this->logger->info('Log entry written with id ' . ($result['id'] ?? 'unknown'));

        $this->json([
            'status' => 'created',
            'id' => isset($result['id']) ? (int) $result['id'] : null,
            'receivedAt' => $result['received_at'] ?? null,
            'rawMessageSize' => isset($result['raw_message_size']) ? (int) $result['raw_message_size'] : null,
            'payloadSha256' => $result['payload_sha256'] ?? null,
        ], 201);
    }

    private function readLogs(): void
    {
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 100;

        $this->json([
            'status' => 'ok',
            'items' => $this->repository->findLatest($limit),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
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

            $name = substr($key, 5);
            $name = str_replace('_', ' ', strtolower($name));
            $name = str_replace(' ', '-', ucwords($name));

            $headers[$name] = (string) $value;
        }

        return $headers;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    private function debugEnabled(): bool
    {
        $value = getenv('APP_DEBUG');

        if ($value === false) {
            return true;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }
}

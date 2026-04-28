<?php

declare(strict_types=1);

/**
 * Kleiner PHP-Client für den aktuellen LogSink-V0/V1-Service.
 *
 * Dieses Beispiel verwendet bewusst nur PHP-Bordmittel:
 *
 * - stream_context_create()
 * - file_get_contents()
 * - $http_response_header
 *
 * Dadurch braucht der Beispielclient weder Composer noch eine zusätzliche
 * cURL-Extension.
 */
final class LogSinkClient
{
    public function __construct(
        private readonly string $baseUrl,
        private readonly int $timeoutSeconds = 15
    ) {
    }

    /**
     * Sendet eine Logmeldung per HTTP POST.
     */
    public function post(string $payload, string $contentType, string $userAgent): array
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => implode("\r\n", [
                    'Content-Type: ' . $contentType,
                    'Accept: application/json',
                    'User-Agent: ' . $userAgent,
                ]),
                'content' => $payload,
                'timeout' => $this->timeoutSeconds,
                'ignore_errors' => true,
            ],
        ]);

        $body = file_get_contents($this->baseUrl, false, $context);
        $headersFromResponse = $http_response_header ?? [];

        return $this->buildResponse($headersFromResponse, $body === false ? '' : $body);
    }

    /**
     * Liest die letzten Logmeldungen per HTTP GET.
     */
    public function getLatest(int $limit = 10): array
    {
        $safeLimit = max(1, min(1000, $limit));
        $url = $this->baseUrl . (str_contains($this->baseUrl, '?') ? '&' : '?') . 'limit=' . $safeLimit;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", [
                    'Accept: application/json',
                    'User-Agent: SASD-php-log-sender-reader/0.1',
                ]),
                'timeout' => $this->timeoutSeconds,
                'ignore_errors' => true,
            ],
        ]);

        $body = file_get_contents($url, false, $context);
        $headersFromResponse = $http_response_header ?? [];

        return $this->buildResponse($headersFromResponse, $body === false ? '' : $body);
    }

    public static function isSuccessful(array $response): bool
    {
        $statusCode = (int) ($response['statusCode'] ?? 0);

        return $statusCode >= 200 && $statusCode < 300;
    }

    public static function printResponse(array $response): void
    {
        echo ($response['statusLine'] ?? 'HTTP status unknown') . PHP_EOL;
        echo PHP_EOL;
        echo ($response['body'] ?? '') . PHP_EOL;
    }

    private function buildResponse(array $headersFromResponse, string $body): array
    {
        $statusLine = $headersFromResponse[0] ?? '';
        $statusCode = $this->parseStatusCode($statusLine);

        $decoded = json_decode($body, true);
        $json = is_array($decoded) ? $decoded : null;

        return [
            'statusCode' => $statusCode,
            'statusLine' => $statusLine,
            'headers' => $headersFromResponse,
            'body' => $body,
            'json' => $json,
        ];
    }

    private function parseStatusCode(string $statusLine): int
    {
        if (preg_match('/^HTTP\/\S+\s+(\d{3})\b/', $statusLine, $matches) === 1) {
            return (int) $matches[1];
        }

        return 0;
    }
}

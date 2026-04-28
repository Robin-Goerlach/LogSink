#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/src/LogSinkClient.php';

$logsinkUrl = getenv('LOGSINK_URL') ?: 'http://api.sasd.de/logsink/index.php';
$client = new LogSinkClient($logsinkUrl);

$payload = json_encode([
    'timestamp' => date(DATE_ATOM),
    'level' => 'ERROR',
    'service' => 'php-error-sender',
    'message' => 'Simulated error from PHP sender',
    'context' => [
        'example' => 'post-error-log.php',
        'exception' => 'DemoException',
        'file' => __FILE__,
        'line' => __LINE__,
        'hint' => 'This is a demo error, not a real application failure.',
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($payload === false) {
    fwrite(STDERR, 'Could not encode JSON payload.' . PHP_EOL);
    exit(1);
}

echo 'POST ' . $logsinkUrl . PHP_EOL;
echo $payload . PHP_EOL . PHP_EOL;

$response = $client->post(
    payload: $payload,
    contentType: 'application/json; charset=utf-8',
    userAgent: 'SASD-php-error-sender/0.1'
);

LogSinkClient::printResponse($response);

exit(LogSinkClient::isSuccessful($response) ? 0 : 1);

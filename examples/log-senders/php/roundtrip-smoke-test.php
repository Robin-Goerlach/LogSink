#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/src/LogSinkClient.php';

$logsinkUrl = getenv('LOGSINK_URL') ?: 'http://api.sasd.de/logsink/index.php';
$client = new LogSinkClient($logsinkUrl);

$runId = sprintf('logsink-php-smoke-%s-%d', date('Ymd\THis'), random_int(1000, 9999));

$payload = json_encode([
    'timestamp' => date(DATE_ATOM),
    'level' => 'INFO',
    'service' => 'php-roundtrip-smoke-test',
    'message' => 'Roundtrip smoke test from PHP sender',
    'context' => [
        'runId' => $runId,
        'expectedFlow' => 'php sender -> service -> database -> reader',
    ],
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($payload === false) {
    fwrite(STDERR, 'Could not encode JSON payload.' . PHP_EOL);
    exit(1);
}

echo 'POST ' . $logsinkUrl . PHP_EOL;
echo 'RUN_ID=' . $runId . PHP_EOL . PHP_EOL;

$postResponse = $client->post(
    payload: $payload,
    contentType: 'application/json; charset=utf-8',
    userAgent: 'SASD-php-roundtrip-smoke-test/0.1'
);

LogSinkClient::printResponse($postResponse);

if (!LogSinkClient::isSuccessful($postResponse)) {
    fwrite(STDERR, 'ERROR: POST failed.' . PHP_EOL);
    exit(1);
}

echo PHP_EOL . 'GET ' . $logsinkUrl . '?limit=10' . PHP_EOL . PHP_EOL;

$getResponse = $client->getLatest(10);
LogSinkClient::printResponse($getResponse);

if (!LogSinkClient::isSuccessful($getResponse)) {
    fwrite(STDERR, 'ERROR: GET failed.' . PHP_EOL);
    exit(1);
}

if (str_contains($getResponse['body'], $runId)) {
    echo PHP_EOL . 'OK: Roundtrip message was found.' . PHP_EOL;
    exit(0);
}

fwrite(STDERR, PHP_EOL . 'ERROR: Roundtrip message was not found in latest logs.' . PHP_EOL);
exit(1);

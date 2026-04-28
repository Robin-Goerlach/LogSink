#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/src/LogSinkClient.php';

$logsinkUrl = getenv('LOGSINK_URL') ?: 'http://api.sasd.de/logsink/index.php';
$client = new LogSinkClient($logsinkUrl);

$payload = sprintf('%s INFO php-text-sender Hello from plain text PHP sender', date(DATE_ATOM));

echo 'POST ' . $logsinkUrl . PHP_EOL;
echo $payload . PHP_EOL . PHP_EOL;

$response = $client->post(
    payload: $payload,
    contentType: 'text/plain; charset=utf-8',
    userAgent: 'SASD-php-text-sender/0.1'
);

LogSinkClient::printResponse($response);

exit(LogSinkClient::isSuccessful($response) ? 0 : 1);

#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * PHP-Reader für die letzten LogSink-Meldungen.
 *
 * Dieses Skript ist das PHP-Gegenstück zu:
 *
 *   examples/log-readers/curl/get-latest-logs.sh
 *
 * Es nutzt bewusst dieselbe kleine LogSinkClient-Klasse wie die PHP-Sender.
 * Für V0 ist das ausreichend. Später können gemeinsame Beispielklassen nach
 * examples/php-common/ verschoben werden.
 */

require __DIR__ . '/../../log-senders/php/src/LogSinkClient.php';

$logsinkUrl = getenv('LOGSINK_URL') ?: 'http://api.sasd.de/logsink/index.php';
$limit = isset($argv[1]) ? (int) $argv[1] : 5;
$limit = max(1, min(1000, $limit));

$client = new LogSinkClient($logsinkUrl);

echo 'GET ' . $logsinkUrl . '?limit=' . $limit . PHP_EOL . PHP_EOL;

$response = $client->getLatest($limit);

LogSinkClient::printResponse($response);

exit(LogSinkClient::isSuccessful($response) ? 0 : 1);

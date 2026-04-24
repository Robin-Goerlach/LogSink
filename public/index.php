<?php

declare(strict_types=1);

/*
 * Öffentlicher Frontcontroller.
 *
 * Ohne .htaccess.
 * Der Webserver kann direkt auf diese Datei zeigen.
 */

require dirname(__DIR__) . '/src/Bootstrap.php';

Sasd\LogSink\Bootstrap::run(dirname(__DIR__));

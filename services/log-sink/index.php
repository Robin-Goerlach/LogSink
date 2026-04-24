<?php

declare(strict_types=1);

/*
 * Root-Frontcontroller.
 *
 * Diese Datei ist absichtlich vorhanden, falls der Webserver versehentlich
 * oder bewusst auf das Projekt-Root zeigt.
 *
 * Empfohlen bleibt:
 *   DocumentRoot -> public/
 */

require __DIR__ . '/public/index.php';

<?php

declare(strict_types=1);

/*
 * Root-Frontcontroller.
 *
 * Diese Datei ist eine kleine Brücke in den eigentlichen öffentlichen
 * Einstiegspunkt unter public/index.php.
 *
 * Warum gibt es diese Datei?
 * --------------------------
 * Idealerweise zeigt ein Webserver direkt auf:
 *
 *   services/log-sink/public/
 *
 * Dann wäre public/index.php der einzige per HTTP erreichbare Einstiegspunkt.
 *
 * Bei Shared-Hosting-Umgebungen, z. B. IONOS, kann man den DocumentRoot aber
 * nicht immer so frei setzen, wie man es bei einem eigenen Server tun würde.
 * Dann kann es passieren, dass der Webserver auf das Projektverzeichnis selbst
 * zeigt. In diesem Fall sorgt diese Datei dafür, dass der Service trotzdem
 * startet.
 *
 * Wichtig:
 * --------
 * Diese Datei enthält keine Anwendungslogik. Sie lädt nur public/index.php.
 * Die eigentliche Initialisierung passiert in:
 *
 *   public/index.php -> Bootstrap::run(...)
 */
require __DIR__ . '/public/index.php';

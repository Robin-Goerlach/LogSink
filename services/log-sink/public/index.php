<?php

declare(strict_types=1);

/*
 * Öffentlicher Frontcontroller des PHP-Services.
 *
 * Diese Datei ist der eigentliche HTTP-Einstiegspunkt des LogSink-Service.
 * Alle HTTP-Requests, die bei diesem PHP-Service landen, sollen letztlich hier
 * starten.
 *
 * Warum ist das wichtig?
 * ----------------------
 * In PHP-Webanwendungen trennt man häufig:
 *
 *   - öffentlich erreichbare Dateien: public/
 *   - internen Anwendungscode:       src/
 *   - Konfiguration/Logs:            .env, var/
 *
 * Der Ordner public/ sollte langfristig der einzige Ordner sein, den ein
 * Webserver direkt ausliefert. Dadurch können Konfigurationsdateien und
 * PHP-Klassen nicht versehentlich als Dateien heruntergeladen werden.
 *
 * Aktueller V1-Stand:
 * -------------------
 * Der Service läuft noch ohne .htaccess und ohne URL-Rewriting. Bei IONOS wird
 * deshalb z. B. diese URL genutzt:
 *
 *   http://api.sasd.de/logsink/index.php?limit=5
 *
 * Später soll daraus eine sauber geroutete API werden, z. B.:
 *
 *   index.php?route=/api/v1/events
 */

/*
 * Bootstrap.php enthält die Startlogik:
 *
 * - Autoloader registrieren
 * - Konfiguration laden
 * - Service-Logger erstellen
 * - Datenbankverbindung vorbereiten
 * - Repository erstellen
 * - App starten
 */
require dirname(__DIR__) . '/src/Bootstrap.php';

/*
 * dirname(__DIR__) ist das Projektverzeichnis services/log-sink.
 *
 * Beispiel:
 *
 *   public/index.php liegt in:
 *   services/log-sink/public/index.php
 *
 *   dirname(__DIR__) ergibt:
 *   services/log-sink
 *
 * Genau diesen Projektpfad braucht Bootstrap, um src/, .env, var/log usw.
 * zuverlässig zu finden.
 */
Sasd\LogSink\Bootstrap::run(dirname(__DIR__));

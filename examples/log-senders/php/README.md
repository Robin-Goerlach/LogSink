# PHP Log Sender Example

Dieser Ordner enthält einen einfachen PHP-Client, der Logmeldungen an den aktuellen LogSink-Service sendet.

## Zweck

Der PHP-Client zeigt, wie eine PHP-Anwendung Logmeldungen an LogSink absetzen kann.

Aktueller Flow:

```text
PHP-Sender -> HTTP POST -> LogSink PHP-Service -> MariaDB
```

Der Roundtrip-Test prüft zusätzlich:

```text
PHP-Sender -> Service -> Datenbank -> GET-API
```

## Voraussetzungen

- PHP CLI, empfohlen PHP 8.1 oder neuer
- Netzwerkzugriff auf den LogSink-Service

Prüfen:

```bash
php -v
```

## Konfiguration

Die Skripte verwenden die Umgebungsvariable `LOGSINK_URL`.

Aktueller IONOS-Testbetrieb:

```bash
export LOGSINK_URL="http://api.sasd.de/logsink/index.php"
```

Lokale Entwicklung mit PHP Development Server:

```bash
export LOGSINK_URL="http://127.0.0.1:8080/api/logs"
```

Wenn `LOGSINK_URL` nicht gesetzt ist, wird als Default genutzt:

```text
http://api.sasd.de/logsink/index.php
```

## Skripte

```text
post-json-log.php          sendet eine einfache JSON-Logmeldung
post-text-log.php          sendet eine einfache Text-Logmeldung
post-error-log.php         sendet eine JSON-Fehlermeldung
roundtrip-smoke-test.php   sendet eine eindeutige Meldung und liest sie danach zurück
src/LogSinkClient.php      kleine wiederverwendbare Client-Klasse
```

## Ausführen

```bash
php examples/log-senders/php/post-json-log.php
php examples/log-senders/php/post-text-log.php
php examples/log-senders/php/post-error-log.php
php examples/log-senders/php/roundtrip-smoke-test.php
```

Oder nach `chmod +x`:

```bash
./examples/log-senders/php/post-json-log.php
./examples/log-senders/php/roundtrip-smoke-test.php
```

## Warum ohne Composer?

Dieses Beispiel bleibt bewusst einfach und benötigt keine externen Pakete.

Für den aktuellen Lernschritt ist wichtig zu verstehen:

- wie ein HTTP POST aus PHP gesendet wird,
- wie Header gesetzt werden,
- wie der Response-Status gelesen wird,
- wie ein Roundtrip-Test funktioniert.

Später kann daraus ein Composer-basiertes Client-Paket entstehen.

## Sicherheitshinweis

Die aktuelle V0/V1-API ist ungeschützt. Später muss dieser Client erweitert werden um:

- Bearer-Token,
- Source-Principal,
- Scope `events.ingest`,
- Timeouts,
- robuste Fehlerbehandlung,
- keine Ausgabe geheimer Tokens.

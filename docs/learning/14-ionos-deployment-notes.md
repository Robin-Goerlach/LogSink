# IONOS Deployment Notes - LogSink V1

## Ziel

Dieses Dokument beschreibt den aktuellen IONOS-Minimalbetrieb der ungeschützten LogSink-V1.

## Aktueller Serverpfad

```text
/homepages/12/d387530851/htdocs/de.sasd/api/logsink
```

Öffentliche URL:

```text
http://api.sasd.de/logsink/index.php
```

## Benötigte Dateien auf dem Server

```text
logsink/
├── index.php
├── public/
│   └── index.php
├── src/
│   ├── App.php
│   ├── Bootstrap.php
│   ├── Config.php
│   ├── Database.php
│   ├── LogRepository.php
│   └── ServiceLogger.php
└── var/
    └── log/
```

## Wichtig: `public/index.php`

Der Root-Frontcontroller `index.php` lädt:

```php
require __DIR__ . '/public/index.php';
```

Wenn `public/index.php` fehlt, entsteht HTTP 500.

## Aktueller V1-Aufruf

```bash
curl -i "http://api.sasd.de/logsink/index.php?limit=5"
```

Erwartung:

```text
HTTP/1.1 200 OK
Content-Type: application/json; charset=utf-8
```

## Warum `/api/logs` nicht funktioniert

Dieser Aufruf:

```text
http://api.sasd.de/logsink/api/logs?limit=5
```

führt ohne `.htaccess` oder Rewrite-Regel zu einer IONOS/Apache-404-Seite.

Der aktuelle V1-Service muss über die echte PHP-Datei angesprochen werden:

```text
http://api.sasd.de/logsink/index.php?limit=5
```

Später soll der shared-hosting-freundliche API-Stil eingeführt werden:

```text
http://api.sasd.de/logsink/index.php?route=/api/v1/events
```

## Externe Konfiguration

Die echte Konfiguration liegt nicht im Service-Verzeichnis.

Zielpfad:

```text
/homepages/.../htdocs/de.sasd/.env-logsink
```

Nicht mehr verwenden:

```text
/homepages/.../htdocs/de.sasd/api/logsink/.env
```

## Konfigurationssuche

`Bootstrap::resolveEnvFile()` sucht:

1. `LOGSINK_ENV_FILE`, falls als Umgebungsvariable gesetzt,
2. zwei Ebenen über dem Service-Verzeichnis: `.env-logsink`,
3. eine Ebene über dem Service-Verzeichnis: `.env-logsink`,
4. lokale Entwicklungsdatei: `.env`.

## Sicherheitschecks

```bash
curl -i "http://api.sasd.de/logsink/.env"
curl -i "http://api.sasd.de/logsink/_.env"
curl -i "http://api.sasd.de/.env-logsink"
```

Keine dieser URLs darf Konfigurationsinhalte liefern.

## Temporäres Diagnose-Skript

Das Diagnose-Skript liegt im Repository unter:

```text
tools/diagnostics/php-diagnose.php
```

Einsatzregel:

1. temporär nach `logsink/php-diagnose.php` kopieren,
2. aufrufen,
3. sofort wieder löschen,
4. 404 prüfen.

## Aktuelle Diagnosewerte

Festgestellt am 2026-04-27:

```text
PHP_VERSION=8.4.20
PHP_SAPI=cgi-fcgi
pdo_mysql=yes
DB_CONNECTION=ok
LOG_ENTRIES=10
```

## Offene Deployment-Aufgaben

- Produktionsmodus `APP_DEBUG=false` vorbereiten.
- `.env-logsink`-Lage endgültig dokumentieren.
- `php-diagnose.php` ggf. in `.example` umbenennen.
- später `var/cache/rate-limit` berücksichtigen.
- später Authentifizierung und Rate-Limits testen.

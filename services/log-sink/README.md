# SASD Log Sink Service

Dieser Ordner enthält den PHP-8.4-Service von SASD LogSink.

## Aufgabe

Der Service nimmt Logmeldungen per HTTP entgegen und speichert den kompletten Request-Body unverändert in MariaDB.

Die aktuelle V1 ist bewusst einfach und noch ungeschützt.

## Lokaler Start

```bash
cd services/log-sink
cp .env.example .env
php -S 127.0.0.1:8080 public/index.php
```

## Lokales Schreiben

```bash
curl -i -X POST "http://127.0.0.1:8080/api/logs"   -H "Content-Type: application/json; charset=utf-8"   --data-binary '{"level":"INFO","service":"demo","message":"Hallo LogSink"}'
```

## Lokales Lesen

```bash
curl -i "http://127.0.0.1:8080/api/logs?limit=10"
```

## IONOS-Testbetrieb

Bei IONOS wurde der Service testweise unter folgendem Pfad betrieben:

```text
/homepages/.../htdocs/de.sasd/api/logsink
```

Öffentliche V1-URL:

```text
http://api.sasd.de/logsink/index.php?limit=5
```

Ohne `.htaccess` oder Rewrite funktioniert dieser Pfad nicht:

```text
http://api.sasd.de/logsink/api/logs
```

Der aktuelle IONOS-Aufruf geht über die echte PHP-Datei:

```text
http://api.sasd.de/logsink/index.php?limit=5
```

## Konfiguration

Lokal wird standardmäßig diese Datei genutzt:

```text
services/log-sink/.env
```

Für IONOS kann die echte Konfiguration außerhalb des Service-Verzeichnisses liegen:

```text
.env-logsink
```

`Bootstrap::resolveEnvFile()` sucht zuerst externe Konfigurationsdateien und fällt danach auf die lokale `.env` zurück.

## Sicherheitshinweis

Eine echte `.env` mit Datenbankzugangsdaten darf nicht im öffentlich erreichbaren Webverzeichnis liegen.

Prüfen:

```bash
curl -i "http://api.sasd.de/logsink/.env"
curl -i "http://api.sasd.de/logsink/_.env"
curl -i "http://api.sasd.de/.env-logsink"
```

Keine dieser URLs darf Secret-Inhalte liefern.

## Temporäre Diagnose

Ein Diagnose-Skript liegt nicht im Service-Verzeichnis, sondern unter:

```text
tools/diagnostics/php-diagnose.php
```

Es darf nur temporär auf den Server kopiert und muss danach sofort wieder gelöscht werden.

## Struktur

```text
services/log-sink/
├── .env.example
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
        └── .gitkeep
```

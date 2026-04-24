# SASD LogSink

**SASD LogSink** ist ein bewusst einfacher PHP-8.4-Logging-Service für MariaDB 10.11.

Der Service nimmt Logmeldungen von Clients per HTTP entgegen und speichert den unveränderten Request-Body in einer MariaDB-Datenbank. Zusätzlich werden technische Request-Metadaten wie IP-Adresse, HTTP-Methode, URI, Content-Type und User-Agent gespeichert.

> Status: V1 / Entwicklungsstand  
> Sicherheit: bewusst ungeschützt, jeder Client darf schreiben und lesen.

## Ziel des Projekts

Dieses Projekt dient als minimaler zentraler Log-Empfänger für Experimente, interne Tests und spätere Erweiterungen.

Die erste Version ist absichtlich klein gehalten:

- kein Framework
- keine `.htaccess`
- eine `index.php` im Projekt-Root
- eine `public/index.php` als öffentlicher Frontcontroller
- einfache `.env`
- einfache Klassenstruktur unter `src/`
- Schreiben per `POST`
- Lesen per `GET`

## Projektstruktur

```text
sasd-log-sink/
├── .env.example
├── .gitignore
├── LICENSE
├── README.md
├── index.php
├── database/
│   └── sasd_logging_mariadb_10_11_demo.sql
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

## Voraussetzungen

- PHP 8.4
- MariaDB 10.11
- PDO MySQL Extension für PHP

## Installation

Repository klonen oder Dateien entpacken:

```bash
git clone <repository-url> sasd-log-sink
cd sasd-log-sink
```

`.env` aus Vorlage erzeugen:

```bash
cp .env.example .env
```

Danach in `.env` das Datenbankpasswort anpassen:

```ini
APP_ENV=dev
APP_DEBUG=true

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sasd_logging
DB_USERNAME=logging_service
DB_PASSWORD=CHANGE_ME_TO_A_LONG_RANDOM_PASSWORD

SERVICE_LOG_ENABLED=true
SERVICE_LOG_FILE=var/log/service.log
SERVICE_LOG_LEVEL=info
```

## Datenbank anlegen

Die Datenbank, Tabelle, Trigger, View und Demo-Daten liegen in:

```text
database/sasd_logging_mariadb_10_11_demo.sql
```

Import-Beispiel:

```bash
mysql -u root -p < database/sasd_logging_mariadb_10_11_demo.sql
```

## Start im PHP Development Server

```bash
php -S 127.0.0.1:8080 public/index.php
```

## API

### Logmeldung schreiben

```http
POST /api/logs
```

Der Body wird unverändert gespeichert.

Beispiel mit JSON:

```bash
curl -i -X POST "http://127.0.0.1:8080/api/logs" \
  -H "Content-Type: application/json; charset=utf-8" \
  --data-binary '{"level":"INFO","service":"demo","message":"Hallo Logging Service"}'
```

Erfolgsantwort:

```json
{
  "status": "created",
  "id": 123,
  "receivedAt": "2026-04-24 10:15:01.123456",
  "rawMessageSize": 67,
  "payloadSha256": "..."
}
```

### Logmeldungen lesen

```http
GET /api/logs?limit=10
```

Beispiel:

```bash
curl -i "http://127.0.0.1:8080/api/logs?limit=10"
```

Antwort:

```json
{
  "status": "ok",
  "items": [
    {
      "id": 123,
      "received_at": "2026-04-24 10:15:01.123456",
      "source_ip": "127.0.0.1",
      "source_port": 54321,
      "http_method": "POST",
      "request_uri": "/api/logs",
      "content_type": "application/json; charset=utf-8",
      "user_agent": "curl/8.0.0",
      "raw_message_size": 67,
      "payload_sha256": "...",
      "raw_message_text": "{...}",
      "raw_message_base64": "..."
    }
  ]
}
```

## Technisches Verhalten

Bei `POST` liest der Service den kompletten HTTP-Body:

```php
file_get_contents('php://input')
```

Dieser Inhalt wird als `raw_message` gespeichert. Die Datenbank berechnet über einen Trigger automatisch:

- `raw_message_size`
- `payload_sha256`

Bei `GET` liest der Service die letzten Logmeldungen aus dem View `v_log_entries`.

## Sicherheitshinweis

Diese V1 ist bewusst ungeschützt. Jeder erreichbare Client kann Logmeldungen schreiben und lesen.

Das ist für spätere produktive Nutzung nicht empfehlenswert. Mögliche spätere Erweiterungen wären:

- API-Key
- Schreib- und Leserechte getrennt
- Rate Limiting
- maximale Payload-Größe
- CORS-Konfiguration
- Mandantenfähigkeit
- strukturierte Suche
- Pagination
- Lösch- oder Archivierungsstrategie

Diese Punkte sind absichtlich noch nicht Bestandteil der V1.

## Lizenz

Dieses Projekt steht unter der MIT-Lizenz. Siehe `LICENSE`.

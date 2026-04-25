# SASD LogSink

**SASD LogSink** ist eine experimentelle Logging-Plattform für die SASD-GmbH. Die aktuelle V1 besteht aus einem einfachen PHP-8.4-Service, einer MariaDB-10.11-Datenbank und einem Java-Swing-Viewer.

> Status: V1 / Entwicklungsstand  
> Sicherheit: bewusst ungeschützt. Jeder erreichbare Client darf schreiben und lesen.  
> Ziel: erst eine verständliche, lauffähige und erweiterbare Basis schaffen.

## Komponenten

| Pfad | Komponente | Beschreibung |
|---|---|---|
| `services/log-sink` | PHP Log Sink Service | Nimmt Logmeldungen per HTTP entgegen und schreibt sie in MariaDB. |
| `clients/java-log-viewer` | Java Log Viewer | Ruft Logmeldungen per `GET` ab und zeigt sie sortierbar in einer Swing-Tabelle an. |
| `database/mariadb` | MariaDB Schema | Erstellt Datenbank, Tabelle, Trigger, View und Demo-Daten. |
| `contracts/http-api` | API-Vertrag | Beschreibt die HTTP-Schnittstelle zwischen Services und Clients. |
| `docs` | Dokumentation | Architektur, Repository-Struktur und Entwicklungshinweise. |
| `scripts` | Hilfsskripte | Kleine Start- und Testskripte für die lokale Entwicklung. |

## Repository-Struktur

```text
LogSink/
├── clients/
│   └── java-log-viewer/
├── contracts/
│   └── http-api/
│       └── logs-v1.md
├── database/
│   └── mariadb/
│       └── mariadb_10_11.sql
├── docs/
│   ├── architecture.md
│   ├── development.md
│   └── repository-structure.md
├── scripts/
│   ├── smoke-test.sh
│   ├── start-java-viewer.sh
│   └── start-php-service.sh
└── services/
    └── log-sink/
        ├── .env.example
        ├── index.php
        ├── public/
        │   └── index.php
        ├── src/
        └── var/
            └── log/
                └── .gitkeep
```

## Voraussetzungen

Für den PHP-Service:

- PHP 8.4
- PHP PDO MySQL Extension
- MariaDB 10.11

Für den Java-Viewer:

- Java 17 oder neuer
- Maven 3.x

## Schnellstart

### 1. Datenbank einrichten

```bash
mysql -u root -p < database/mariadb/mariadb_10_11.sql
```

Das Skript legt die Demo-Datenbank `sasd_logging`, die Tabelle `log_entries`, den Trigger, den View und 10 Demo-Logmeldungen an.

### 2. PHP-Service konfigurieren

```bash
cd services/log-sink
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

### 3. PHP-Service starten

Aus dem Repository-Root:

```bash
./scripts/start-php-service.sh
```

Oder manuell:

```bash
cd services/log-sink
php -S 127.0.0.1:8080 public/index.php
```

### 4. Schreiben testen

```bash
curl -i -X POST "http://127.0.0.1:8080/api/logs" \
  -H "Content-Type: application/json; charset=utf-8" \
  --data-binary '{"level":"INFO","service":"demo","message":"Hallo LogSink"}'
```

### 5. Lesen testen

```bash
curl -i "http://127.0.0.1:8080/api/logs?limit=10"
```

### 6. Java-Viewer starten

```bash
cd clients/java-log-viewer
mvn clean package
java -jar target/sasd-log-viewer-java-1.0.0.jar
```

## HTTP API V1

Die aktuelle Schnittstelle ist absichtlich klein:

```text
POST /api/logs
GET  /api/logs?limit=100
```

Der Service speichert den Request-Body bei `POST` unverändert als `raw_message`.

Details stehen in:

```text
contracts/http-api/logs-v1.md
```

## Sicherheitshinweis

Diese V1 ist absichtlich ungeschützt. Das entspricht dem aktuellen Entwicklungsziel, ist aber nicht produktionsreif.

Bekannte offene Punkte für spätere Versionen:

- Authentifizierung, z. B. API-Key
- getrennte Lese- und Schreibrechte
- maximale Payload-Größe
- Rate Limiting
- Pagination mit Offset oder Cursor
- strukturierte Suche
- Aufbewahrungsfristen und Archivierung
- Mandantenfähigkeit

Diese Punkte sind bewusst nicht Bestandteil der V1.

## Changelog

Siehe [`CHANGELOG.md`](CHANGELOG.md).

## Lizenz

Dieses Projekt steht unter der MIT-Lizenz. Siehe [`LICENSE`](LICENSE).

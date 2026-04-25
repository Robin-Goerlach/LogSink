# Beginner Guide - praktische Grundlagen für LogSink

Dieses Dokument erklärt das Drumherum für Neulinge. Es ergänzt den Lehrplan.

## 1. Repository verstehen

### Wo ist was?

| Pfad | Bedeutung |
|---|---|
| `services/log-sink` | PHP-Service |
| `clients/java-log-viewer` | Java-Swing-Client |
| `database/mariadb` | SQL-Dateien |
| `contracts/http-api` | API-Verträge |
| `docs/learning` | Lehrplan und Projektgedächtnis |
| `scripts` | Hilfsskripte |

## 2. PHP-Service starten

```bash
cd services/log-sink
cp .env.example .env
php -S 127.0.0.1:8080 public/index.php
```

### Was passiert hier?

- `cp .env.example .env` erzeugt deine lokale Konfiguration.
- `php -S` startet den eingebauten PHP-Entwicklungsserver.
- `public/index.php` ist der öffentliche Einstiegspunkt.
- Der Service liest `.env`, verbindet sich mit MariaDB und verarbeitet HTTP-Requests.

### Häufige Fehler

| Fehler | Ursache | Lösung |
|---|---|---|
| `Connection refused` | Service läuft nicht | PHP-Server starten |
| Datenbankfehler | `.env` passt nicht | DB_HOST, DB_USERNAME, DB_PASSWORD prüfen |
| 500 Fehler | PHP-Exception | `services/log-sink/var/log/service.log` prüfen |

## 3. curl verstehen

`curl` ist ein Kommandozeilenwerkzeug für HTTP-Requests.

### GET

```bash
curl -i "http://127.0.0.1:8080/api/logs?limit=10"
```

### POST

```bash
curl -i -X POST "http://127.0.0.1:8080/api/logs" \
  -H "Content-Type: application/json" \
  --data-binary '{"message":"Hallo"}'
```

### Später mit Bearer-Token

```bash
curl -i -X POST "http://127.0.0.1:8080/index.php?route=/api/v1/ingest/events" \
  -H "Authorization: Bearer DEV_SOURCE_TOKEN" \
  -H "Content-Type: application/json" \
  --data-binary '{"message":"Hallo"}'
```

Wichtig: `curl` selbst hat keine Rechte. Rechte entstehen durch den Token, den du mitsendest.

## 4. Java-Client starten

```bash
cd clients/java-log-viewer
mvn clean package
java -jar target/sasd-log-viewer-java-1.0.0.jar
```

### Was ist Maven?

Maven ist ein Build-Werkzeug für Java.

Die Datei `pom.xml` beschreibt:

- Projektname,
- Java-Version,
- Abhängigkeiten,
- Build-Plugins,
- Main-Klasse.

### Was macht `mvn clean package`?

- `clean` löscht alte Build-Ergebnisse.
- `package` kompiliert den Code und erzeugt eine JAR-Datei.
- Die JAR liegt unter `target/`.

### Häufige Fehler

| Fehler | Ursache | Lösung |
|---|---|---|
| `mvn: command not found` | Maven fehlt | Maven installieren |
| `Unsupported class file major version` | falsche Java-Version | Java 17+ verwenden |
| Client zeigt Fehler | Service läuft nicht oder URL falsch | Service starten, URL prüfen |

## 5. Composer verstehen

Composer ist das Paket- und Autoloading-Werkzeug für PHP.

Wir führen Composer später ein.

### Typische Befehle

```bash
composer init
composer install
composer require --dev phpunit/phpunit
composer dump-autoload
```

### Wichtige Dateien

| Datei/Ordner | Bedeutung |
|---|---|
| `composer.json` | Projektdefinition |
| `composer.lock` | exakte Paketversionen |
| `vendor/` | installierte Pakete |
| `vendor/autoload.php` | Autoloader |

### Git-Regel

- `vendor/` kommt nicht ins Git.
- `composer.json` kommt ins Git.
- `composer.lock` bei Anwendungen normalerweise ja, bei Libraries manchmal nein. Für unseren Service als Anwendung kann `composer.lock` sinnvoll ins Git.

## 6. PHPUnit verstehen

PHPUnit ist das wichtigste Testwerkzeug für PHP.

### Späterer Start

```bash
cd services/log-sink
vendor/bin/phpunit
```

### Testarten

| Testart | Zweck |
|---|---|
| Unit-Test | einzelne Klasse prüfen |
| Integrationstest | mehrere Klassen mit DB oder HTTP prüfen |
| End-to-End-Test | echter HTTP-Request gegen laufenden Service |

## 7. phpDocumentor verstehen

phpDocumentor erzeugt Code-Dokumentation aus PHPDoc-Kommentaren.

### Warum?

- Klassen und Methoden werden verständlicher.
- Öffentliche APIs werden dokumentiert.
- Anfänger lernen, was eine Klasse tun soll.

### Späterer Befehl

```bash
vendor/bin/phpdoc
```

## 8. Konfiguration finden

### Service

```text
services/log-sink/.env
services/log-sink/.env.example
```

Aktuelle einfache Werte:

```ini
APP_ENV=dev
APP_DEBUG=true

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sasd_logging
DB_USERNAME=logging_service
DB_PASSWORD=...
```

Später werden daraus mehr Werte, z. B.:

```ini
APP_ROUTE_PARAM=route
APP_MAX_JSON_BYTES=262144
AUTH_TOKEN_PEPPER=
RATE_LIMIT_ENABLED=true
```

### Client

Aktuell noch UI-Felder. Später:

```text
clients/java-log-viewer/client-settings.json
clients/java-log-viewer/client-settings.example.json
```

## 9. Testauswertung

### HTTP-Statuscodes

| Status | Bedeutung |
|---|---|
| 200 | Lesen erfolgreich |
| 202 | Ingest angenommen |
| 400 | Request formal falsch |
| 401 | keine/ungültige Authentifizierung |
| 403 | Token vorhanden, aber keine Berechtigung |
| 404 | Route oder Datensatz nicht gefunden |
| 413 | Payload zu groß |
| 415 | falscher Content-Type |
| 422 | fachliche Validierung fehlgeschlagen |
| 429 | Rate-Limit erreicht |
| 500 | interner Fehler |

### Beispiel

```bash
curl -i ...
```

`-i` zeigt Header und Statuscode. Das ist für Tests wichtig.

## 10. IONOS-Deployment grob einordnen

### Shared Hosting

Mögliche Herausforderungen:

- DocumentRoot eventuell nicht frei setzbar.
- `.env` muss geschützt liegen.
- PHP-Version prüfen.
- MariaDB-Zugangsdaten aus IONOS verwenden.
- Schreibrechte für `var/log` und später `var/cache/rate-limit`.

### VPS/Server

Mehr Kontrolle:

- PHP-Version installierbar.
- Webserver konfigurierbar.
- HTTPS/Firewall selbst verwalten.
- DocumentRoot sauber auf `public/` möglich.

## 11. Autorisierung mit curl

Später:

```bash
curl -i \
  -H "Authorization: Bearer DEV_CLIENT_TOKEN" \
  "http://127.0.0.1:8080/index.php?route=/api/v1/events"
```

Wichtig:

- Token ohne Anführungszeichen im Header.
- Richtiger Token-Typ für richtigen Endpunkt.
- `source` schreibt.
- `client` liest.

## 12. Fehlersuche

Erst fragen:

1. Läuft der PHP-Service?
2. Stimmt die URL?
3. Stimmt die Route?
4. Ist die Datenbank erreichbar?
5. Ist `.env` korrekt?
6. Ist der Content-Type korrekt?
7. Ist der Token korrekt?
8. Hat der Token den richtigen Scope?
9. Sieht der Server die erwartete IP?
10. Gibt es Einträge in `var/log/service.log`?

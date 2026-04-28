# LogSink V1 - Code-Walkthrough und erste Sicherungsmaßnahme

## Zweck

Dieses Dokument beschreibt den aktuellen funktionierenden Anfangszustand von LogSink.

Es erklärt:

- welche Teile aktuell vorhanden sind,
- wie SQL, PHP-Service und Java-Viewer zusammenspielen,
- wie der Service bei IONOS lauffähig wurde,
- welche erste Sicherungsmaßnahme bereits umgesetzt wurde,
- welche Punkte noch bewusst unsicher sind.

## Aktueller Systemfluss

```text
Java-Viewer oder curl
        |
        | HTTP GET/POST
        v
PHP-Service
        |
        | PDO / SQL
        v
MariaDB
```

Die aktuelle V1 kennt im Kern:

```text
GET  index.php?limit=5
POST index.php
```

Bei IONOS ohne Rewrite wird der echte Dateipfad genutzt:

```text
http://api.sasd.de/logsink/index.php?limit=5
```

## SQL: aktuelle Datenbank

Die SQL-Dateien liegen unter:

```text
database/mariadb/
├── 000_create_database_local.sql
├── 001_schema_existing_database.sql
└── 010_demo_data.sql
```

### `000_create_database_local.sql`

Diese Datei ist für lokale Entwicklung gedacht. Sie darf Befehle wie `CREATE DATABASE` und `USE` enthalten.

Bei IONOS darf der Datenbanknutzer in der Regel keine Datenbank per SQL anlegen. Deshalb wird diese Datei dort nicht verwendet.

### `001_schema_existing_database.sql`

Diese Datei erzeugt das Schema in einer bereits ausgewählten Datenbank.

Wichtige Objekte:

- Tabelle `log_entries`,
- Trigger zur Berechnung von Größe und Hash,
- View `v_log_entries`.

### `010_demo_data.sql`

Diese Datei fügt Demo-Logmeldungen ein. Sie ist nützlich für:

- ersten Service-Test,
- Java-Viewer-Test,
- UI-Sortierung,
- Filtertests.

## Tabelle `log_entries`

Die Tabelle speichert Rohdaten.

Wichtige Felder:

| Feld | Bedeutung |
|---|---|
| `id` | technische ID |
| `received_at` | Zeitpunkt der Speicherung |
| `raw_message` | unveränderte Logmeldung |
| `source_ip` | erkannte Quell-IP |
| `source_port` | erkannter Quell-Port |
| `http_method` | GET/POST etc. |
| `request_uri` | angefragte URI |
| `content_type` | HTTP Content-Type |
| `user_agent` | HTTP User-Agent |
| `raw_message_size` | Größe der Rohmeldung |
| `payload_sha256` | Hash der Rohmeldung |

## Warum `raw_message`?

Die erste V1 speichert bewusst unverändert. Dadurch muss der Service am Anfang noch nicht wissen, welches Logformat der Client sendet.

Das ist didaktisch einfach, aber noch nicht das Zielsystem. Später kommen strukturierte Events.

## PHP-Service: Einstiegspunkte

### `services/log-sink/index.php`

Diese Datei ist der Root-Frontcontroller.

Sie lädt:

```php
require __DIR__ . '/public/index.php';
```

Bei IONOS war wichtig: `public/index.php` muss wirklich mit hochgeladen werden. Fehlt diese Datei, entsteht HTTP 500.

### `services/log-sink/public/index.php`

Diese Datei startet den eigentlichen Service:

```php
require dirname(__DIR__) . '/src/Bootstrap.php';

Sasd\LogSink\Bootstrap::run(dirname(__DIR__));
```

Sie bestimmt den Projektroot und übergibt ihn an `Bootstrap`.

## PHP-Service: Klassen

### `Bootstrap.php`

Aufgabe:

- Autoloader registrieren,
- Konfiguration laden,
- Logger, Datenbank, Repository und App verdrahten,
- App starten.

Wichtiger neuer Punkt:

```php
Config::fromEnvFile(self::resolveEnvFile($projectRoot))
```

Damit wird nicht mehr nur `services/log-sink/.env` gesucht, sondern auch eine externe `.env-logsink`.

### `Config.php`

Aufgabe:

- `.env` / `.env-logsink` lesen,
- Werte als String, Integer oder Boolean bereitstellen,
- Defaultwerte ermöglichen.

### `Database.php`

Aufgabe:

- PDO-Verbindung zur MariaDB herstellen,
- DSN aus Host, Port und Datenbankname bauen,
- Fehler über Exceptions sichtbar machen.

### `LogRepository.php`

Aufgabe:

- SQL für Schreiben und Lesen kapseln,
- Logmeldungen in `log_entries` schreiben,
- die letzten Logmeldungen aus `v_log_entries` lesen.

### `App.php`

Aufgabe:

- HTTP-Methode auswerten,
- bei `POST` Logmeldung schreiben,
- bei `GET` Logmeldungen lesen,
- JSON-Antwort senden,
- Fehler abfangen.

Die V1 ist hier noch sehr simpel. Es gibt noch keinen echten Router.

### `ServiceLogger.php`

Aufgabe:

- internes technisches Service-Log schreiben,
- nicht zu verwechseln mit den fachlichen Logmeldungen der Clients.

## Java-Viewer

Der Java-Client liegt unter:

```text
clients/java-log-viewer
```

Build:

```bash
mvn -f clients/java-log-viewer/pom.xml clean package
```

Start:

```bash
java -jar clients/java-log-viewer/target/sasd-log-viewer-java-1.0.0.jar
```

### `Main.java`

Startet die Swing-Anwendung.

### `LogViewerFrame.java`

Hauptfenster:

- URL-Feld,
- Limit-Auswahl,
- Filter,
- Tabelle,
- Aktualisieren-Button,
- Detaildialog.

Aktuelles Provisorium:

```java
private static final String DEFAULT_SERVICE_URL = "http://api.sasd.de/logsink/index.php";
```

Das soll später in eine Konfigurationsdatei.

### `LogServiceClient.java`

Ruft per HTTP GET den Service auf. Er hängt `limit` an die URL an.

### `LogResponse.java`

Bildet das aktuelle V1-Antwortformat ab:

```json
{
  "status": "ok",
  "items": []
}
```

### `LogEntry.java`

Bildet eine Logzeile aus JSON auf ein Java-Objekt ab.

### `LogTableModel.java`

Macht aus den Logeinträgen eine Swing-Tabelle.

## Erste Sicherungsmaßnahme: `.env` aus Browser-Reichweite

### Problem

Die `.env` war zeitweise unter dieser URL erreichbar:

```text
http://api.sasd.de/logsink/.env
```

Das ist ein schweres Sicherheitsproblem, weil dort Datenbankzugangsdaten stehen können.

### Lösung

Die echte IONOS-Konfiguration liegt jetzt außerhalb des Service-Verzeichnisses:

```text
/homepages/.../htdocs/de.sasd/.env-logsink
```

Der Service findet diese Datei über:

```php
Bootstrap::resolveEnvFile()
```

### Tests

```bash
curl -i "http://api.sasd.de/logsink/.env"
curl -i "http://api.sasd.de/logsink/_.env"
curl -i "http://api.sasd.de/.env-logsink"
```

Keine dieser URLs darf Konfigurationsinhalte liefern.

## Was ist weiterhin unsicher?

Trotz erster Sicherungsmaßnahme ist die V1 noch offen:

- keine Authentifizierung,
- keine Autorisierung,
- jeder erreichbare Client darf lesen,
- jeder erreichbare Client darf schreiben,
- keine Payload-Grenze,
- keine strukturierte Validierung,
- kein Rate-Limit,
- kein Audit.

## Warum ist der Stand trotzdem wertvoll?

Weil jetzt ein echter, funktionierender End-to-End-Stand existiert:

```text
IONOS PHP 8.4 -> MariaDB -> JSON -> Java-Viewer
```

Ab jetzt können wir jede Schutzschicht einzeln einbauen und testen.

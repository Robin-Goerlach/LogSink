# Learning Plan Addendum - 2026-04-27

Dieses Addendum ergänzt `03-learning-plan.md` um praktische Schritte aus der Montagmorgen-Arbeit: Maven-Build, curl-Diagnose, IONOS-Dateistruktur, externe `.env-logsink`, Diagnose-Skript und SQL-Splitting.

## LS-013a: Java-Client mit Maven aus Repository-Root bauen

### Ziel

Den Java-Client bauen, ohne in das Client-Verzeichnis wechseln zu müssen.

### Warum

Das Repository ist ein Monorepo. Im Root liegt keine `pom.xml`; die `pom.xml` liegt unter `clients/java-log-viewer`.

### Befehl

```bash
mvn -f clients/java-log-viewer/pom.xml clean package
```

### Erwartung

```text
BUILD SUCCESS
```

### Typischer Fehler

```text
The goal you specified requires a project to execute but there is no POM in this directory
```

Ursache: Maven wurde im Repository-Root ohne `-f clients/java-log-viewer/pom.xml` gestartet.

## LS-013b: Remote-Service mit curl diagnostizieren

### Ziel

Klären, ob ein Fehler vom Java-Client, vom Webserver oder vom PHP-Service kommt.

### Tests

```bash
curl -i "http://api.sasd.de/logsink/api/logs?limit=5"
curl -i "http://api.sasd.de/logsink/index.php?limit=5"
curl -i "http://api.sasd.de/logsink/public/index.php?limit=5"
```

### Ergebnis

- `/logsink/api/logs` führt ohne Rewrite zu IONOS/Apache-404.
- `/logsink/index.php?limit=5` ist für V1 der richtige Aufruf.
- Fehlendes `public/index.php` führte zu HTTP 500.
- Eine öffentlich erreichbare `.env` war ein Sicherheitsproblem.

## LS-016a: IONOS-Dateistruktur verstehen

### Ziel

Verstehen, welche Dateien auf dem Server liegen müssen.

### Minimale Struktur

```text
logsink/
├── index.php
├── public/
│   └── index.php
├── src/
└── var/log/
```

`logsink/index.php` lädt:

```php
require __DIR__ . '/public/index.php';
```

Fehlt `public/index.php`, entsteht HTTP 500.

## LS-016b: `.env` aus dem öffentlichen Webpfad entfernen

### Ziel

Verhindern, dass Zugangsdaten per HTTP abrufbar sind.

### Zielzustand

```bash
curl -i "http://api.sasd.de/logsink/.env"
curl -i "http://api.sasd.de/logsink/_.env"
curl -i "http://api.sasd.de/.env-logsink"
```

Keine dieser URLs darf Konfigurationsinhalte liefern.

### Umsetzung

Die echte Konfiguration liegt außerhalb des Service-Verzeichnisses als `.env-logsink`.

Der Service sucht sie über `Bootstrap::resolveEnvFile()`.

## LS-016c: Diagnose-Skript kontrolliert verwenden

### Ziel

Diagnose ermöglichen, ohne das Skript dauerhaft online zu lassen.

### Repository-Ort

```text
tools/diagnostics/php-diagnose.php
```

### Einsatzregel

1. temporär nach `logsink/php-diagnose.php` kopieren,
2. Diagnose ausführen,
3. sofort wieder löschen,
4. mit curl prüfen, dass es nicht mehr erreichbar ist.

## LS-017: MariaDB-Skripte für lokal und IONOS trennen

### Ziel

SQL-Dateien so strukturieren, dass sie lokal und bei IONOS ohne manuelles Auskommentieren nutzbar sind.

### Zielstruktur

```text
database/mariadb/
├── 000_create_database_local.sql
├── 001_schema_existing_database.sql
└── 010_demo_data.sql
```

### Nutzung lokal

```bash
mysql -u root -p < database/mariadb/000_create_database_local.sql
mysql -u root -p sasd_logging < database/mariadb/001_schema_existing_database.sql
mysql -u root -p sasd_logging < database/mariadb/010_demo_data.sql
```

### Nutzung IONOS

In der bestehenden IONOS-Datenbank nur ausführen:

```text
001_schema_existing_database.sql
010_demo_data.sql
```

## LS-018: Dokument "Von ungeschützt zu sicher" beginnen

### Ziel

Den Sicherheitsausbau als lesbares Langdokument begleiten.

### Datei

```text
docs/learning/13-from-unprotected-to-secure.md
```

## LS-019: Java-Client-Konfiguration statt hart codierter URL

### Ziel

Die Service-URL nicht mehr im Java-Code ändern müssen.

### Aktuelles Provisorium

```java
private static final String DEFAULT_SERVICE_URL = "http://api.sasd.de/logsink/index.php";
```

### Ziel

```text
clients/java-log-viewer/client-settings.example.json
clients/java-log-viewer/client-settings.json
```

Beispiel für V1:

```json
{
  "serviceUrl": "http://api.sasd.de/logsink/index.php",
  "defaultLimit": 100,
  "timeoutSeconds": 15
}
```

## LS-020: Code stärker kommentieren

### Ziel

Service- und Client-Code so kommentieren, dass ein Anfänger die Zuständigkeiten versteht.

### Schwerpunkt

- `Bootstrap.php`
- `Config.php`
- `Database.php`
- `LogRepository.php`
- `App.php`
- `ServiceLogger.php`
- `LogServiceClient.java`
- `LogViewerFrame.java`
- `LogTableModel.java`

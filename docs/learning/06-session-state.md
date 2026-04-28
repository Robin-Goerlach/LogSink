# Session State - SASD LogSink

## Aktueller Stand

Datum: 2026-04-28  
Sitzung: Dienstag  
Arbeitsmodus: Lehrprojekt, atomare Schritte, Service/Datenbank/Client gemeinsam betrachten.

## Git-Stand zuletzt bekannt

Aktueller sauberer Stand nach der Client-Beispielrunde und Dokumentationskonsolidierung:

```text
main
origin/main
working tree clean
```

Wichtige letzte Commits:

```text
16b0af1 Update TODO after client examples
d1d7312 Consolidate documentation after client examples
b539231 Add C# log sender and reader examples
58295a5 Add Java log sender example
6946536 Merge PHP sender/reader examples
```

## Erreichter Stand

Der PHP-Service läuft bei IONOS.

Aktueller Remote-Endpunkt:

```text
http://api.sasd.de/logsink/index.php
```

Die echte IONOS-Konfiguration wurde aus der Browser-Reichweite verschoben:

```text
.env-logsink
```

Die HTTP-API ist weiterhin ungeschützt. Die erste Sicherungsmaßnahme schützt nur die Konfigurationsdatei, nicht den Service-Endpunkt.

## Datenbank/Testbestand

Die Datenbank enthält aktuell 24 nachvollziehbare Testmeldungen:

```text
1-10    SQL-Demo-Daten
11-12   curl-Sender und curl-Roundtrip
13-16   PHP-Sender und PHP-Roundtrip
17-20   Java-Sender und Java-Roundtrip
21-24   C#-Sender und C#-Roundtrip
```

Dieser Testbestand ist im Moment nützlich, weil er die Entwicklungsschritte im Java-Viewer und über die Reader-Beispiele sichtbar macht.

## Aktuelle technische Basis

### Service

```text
services/log-sink/
```

Remote-Test:

```bash
curl -i "http://api.sasd.de/logsink/index.php?limit=5"
```

### Datenbank

```text
database/mariadb/
├── 000_create_database_local.sql
├── 001_schema_existing_database.sql
├── 010_demo_data.sql
└── README.md
```

### Java-Viewer

Der Java-Viewer baut mit:

```bash
mvn -f clients/java-log-viewer/pom.xml clean package
```

Der Java-Viewer nutzt Konfigurationsdateien:

```text
clients/java-log-viewer/client-settings.example.json
clients/java-log-viewer/client-settings.json
```

`client-settings.example.json` wird committed.  
`client-settings.json` wird lokal verwendet und ignoriert.

## Beispiel-Clients

Die aktuelle V0/V1-Client-Beispielrunde ist abgeschlossen.

### curl

```text
examples/log-senders/curl/
examples/log-readers/curl/
```

### PHP

```text
examples/log-senders/php/
examples/log-readers/php/
```

### Java

```text
examples/log-senders/java/
```

### C#

```text
examples/csharp/
```

## Aktueller API-Vertrag

Der reale aktuelle Vertrag ist dokumentiert in:

```text
contracts/http-api/logs-v1.md
```

Wichtig: Der Vertrag beschreibt den aktuellen V0/V1-Stand über `index.php`. Die geplante Ziel-API mit Routing, Request-ID, einheitlichem Antwortmodell, Authentifizierung und Scopes kommt später.

## Nächste Arbeitsschritte

Der nächste sinnvolle technische Schritt ist:

```text
LS-021: Request-ID einführen
```

Danach folgen:

```text
LS-022: Einheitliches JSON-Antwortmodell einführen
LS-023: Einfaches Routing einführen
LS-024: Front-Controller-Route-Parameter ergänzen
```

## Warnung

Die V0/V1 ist weiterhin ungeschützt. Die Beispiel-Clients curl, PHP, Java und C# nutzen bewusst noch den offenen Endpunkt. Vor produktiver Nutzung müssen mindestens Authentifizierung, Autorisierung, Scopes, Audit-Logging und defensive Fehlerbehandlung ergänzt werden.

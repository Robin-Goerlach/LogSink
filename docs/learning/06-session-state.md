# Session State - SASD LogSink

## Aktueller Stand

Datum: 2026-04-28  
Sitzung: Dienstagmorgen  
Arbeitsmodus: Lehrprojekt, atomare Schritte, Service/Datenbank/Client gemeinsam betrachten.

## Rückblick Montag

Am Montag wurde ein wichtiger End-to-End-Stand erreicht:

- PHP-Service läuft bei IONOS.
- Java-Viewer kann Remote-Logs anzeigen.
- IONOS nutzt PHP 8.4.20.
- PDO MySQL ist verfügbar.
- Die Testdatenbank enthält 10 Demo-Logmeldungen.
- `public/index.php` wurde als notwendiger Deployment-Bestandteil erkannt.
- `.env` wurde aus dem öffentlichen Service-Verzeichnis entfernt.
- externe `.env-logsink` wurde eingeführt.
- SQL wurde in lokale Datenbankerzeugung, Schema und Demo-Daten getrennt.
- Diagnose-Skript wurde in `tools/diagnostics` verschoben.

## Aktuelle technische Basis

### Service

V1-URL bei IONOS:

```text
http://api.sasd.de/logsink/index.php?limit=5
```

Erwartetes Ergebnis:

```json
{
  "status": "ok",
  "items": []
}
```

### Datenbank

```text
database/mariadb/
├── 000_create_database_local.sql
├── 001_schema_existing_database.sql
└── 010_demo_data.sql
```

### Konfiguration

Echte IONOS-Konfiguration:

```text
.env-logsink
```

Nicht mehr öffentlich im Service-Verzeichnis:

```text
logsink/.env
```

### Java-Viewer

Der Java-Viewer baut mit:

```bash
mvn -f clients/java-log-viewer/pom.xml clean package
```

Die URL ist aktuell noch provisorisch im Code. Das soll durch eine Konfigurationsdatei ersetzt werden.

## Neuer wichtiger Punkt

Bisher wurde hauptsächlich der Java-Viewer betrachtet. Es fehlen schreibende Beispiel-Clients.

Für das Projekt werden benötigt:

- curl-Beispiele,
- PHP-Logging-Client,
- Java-Logging-Client,
- später ggf. JavaScript/Node und C#/.NET.

Diese Sender-Clients sollen mit dem Service mitwachsen:

```text
ungeschütztes POST -> strukturierter JSON-Body -> Bearer-Token -> Source-Principal -> Scope events.ingest
```

## Dokumentationsbereinigung

Es gab zwei ähnlich benannte Dokumente:

```text
10-from-unprotected-to-secure.md
13-from-unprotected-to-secure.md
```

Kanonisch soll künftig sein:

```text
10-from-unprotected-to-secure.md
```

`13-from-unprotected-to-secure.md` wird entfernt.

## Nächste Arbeitsschritte

1. Dokumentation bereinigen und committen.
2. Doppelte Sicherheitsdatei entfernen.
3. `11-v1-code-walkthrough-and-first-hardening.md` übernehmen.
4. `12-logging-client-plan.md` übernehmen.
5. Code-Kommentierung vorbereiten.
6. Danach MariaDB-README prüfen.
7. Danach Java-Client-Konfiguration angehen.
8. Danach schreibende Beispiel-Clients planen/umsetzen.

## Warnung

Die V1 ist weiterhin ungeschützt. Die erste Sicherungsmaßnahme schützt nur die Konfigurationsdatei, nicht die HTTP-API.

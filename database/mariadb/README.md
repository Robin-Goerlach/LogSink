# MariaDB Scripts

Dieser Ordner enthält die SQL-Dateien für die aktuelle LogSink-V1.

## Dateien

```text
database/mariadb/
├── 000_create_database_local.sql
├── 001_schema_existing_database.sql
└── 010_demo_data.sql
```

## Zweck der Aufteilung

Bei lokaler Entwicklung ist es praktisch, die Datenbank per SQL anzulegen. Bei IONOS darf der Datenbanknutzer in der Regel keine Datenbank per `CREATE DATABASE` erzeugen. Dort wird die Datenbank über die Hosting-Oberfläche angelegt und im SQL-Tool ausgewählt.

## Lokale Nutzung

```bash
mysql -u root -p < database/mariadb/000_create_database_local.sql
mysql -u root -p sasd_logging < database/mariadb/001_schema_existing_database.sql
mysql -u root -p sasd_logging < database/mariadb/010_demo_data.sql
```

## IONOS-Nutzung

In IONOS die vorhandene Datenbank auswählen und nur diese Dateien ausführen:

```text
001_schema_existing_database.sql
010_demo_data.sql
```

## Aktuelles V1-Datenmodell

Die aktuelle V1 speichert Rohmeldungen.

Wichtige Objekte:

- Tabelle `log_entries`
- Trigger zur Berechnung von Größe und SHA-256-Hash
- View `v_log_entries`
- Demo-Daten mit 10 Logmeldungen

## Späteres Ziel

Das spätere Zielmodell wird strukturierte Events enthalten, unter anderem:

- `ingest_requests`
- `log_events`
- `access_audit`
- `log_source_networks`
- Credential-/Token-Tabellen
- `api_v1_log_events`
- `api_v1_log_sources`

Die aktuelle Tabelle `log_entries` bleibt zunächst als Legacy-/V0-Basis erhalten.

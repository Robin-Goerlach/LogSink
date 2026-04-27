# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format orientiert sich lose an Keep a Changelog, ohne bereits eine formale Release-Policy vorauszusetzen.

## [Unreleased]

### Added

- Learning-Dokumentation für den Montagmorgen-Stand am 2026-04-27.
- IONOS-Deployment-Notizen für den aktuell laufenden V1-Service.
- Ergänzende LS-Schritte für Maven-Build, curl-Diagnose, IONOS-Dateistruktur, externe `.env-logsink`, SQL-Splitting, Java-Client-Konfiguration und Code-Kommentierung.
- Dokument "Von ungeschützt zu sicher" als Sicherheits-Lernpfad.

### Changed

- Service-Konfiguration kann über externe `.env-logsink` außerhalb des Service-Verzeichnisses geladen werden.
- MariaDB-Skript wurde in lokale Datenbankerzeugung, Schema für bestehende Datenbank und Demo-Daten getrennt.
- Temporäres PHP-Diagnose-Skript wurde aus dem Service-Kontext in `tools/diagnostics` verschoben.

### Security

- Öffentlich erreichbare `.env` im IONOS-Service-Verzeichnis wurde entfernt.
- Webchecks für `.env`, `_.env` und `.env-logsink` wurden dokumentiert.
- Diagnose-Skript darf nicht dauerhaft auf dem Server liegen.

## [0.1.0] - 2026-04-25

### Added

- Monorepo-Struktur mit getrennten Bereichen für Services, Clients, Datenbank, Verträge und Dokumentation.
- PHP-Service unter `services/log-sink`.
- Java-Swing-Viewer unter `clients/java-log-viewer`.
- MariaDB-10.11-Skript unter `database/mariadb/mariadb_10_11.sql`.
- HTTP-API-Vertrag unter `contracts/http-api/logs-v1.md`.
- Root-README für das Gesamtprojekt.
- Service-spezifische README für den PHP-Service.
- Client-spezifische README für den Java-Viewer.
- Architektur- und Entwicklungshinweise unter `docs/`.
- Hilfsskripte unter `scripts/`.

### Changed

- Die Projektstruktur wurde von einer flachen PHP-Service-Struktur zu einer erweiterbaren Monorepo-Struktur umgebaut.
- Der PHP-Service ist eindeutig unter `services/log-sink` eingeordnet.
- Die Datenbankdatei liegt nun unter `database/mariadb/mariadb_10_11.sql`.

### Security

- Keine Schutzmechanismen eingebaut. Die V1 bleibt bewusst ungeschützt.

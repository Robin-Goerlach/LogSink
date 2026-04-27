# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format orientiert sich lose an Keep a Changelog, ohne bereits eine formale Release-Policy vorauszusetzen.

## [Unreleased]

### Added

- Learning-Dokumentation für den Montagmorgen-Stand am 2026-04-27.
- IONOS-Deployment-Notizen für den aktuell laufenden V1-Service.
- Ergänzende LS-Schritte für Maven-Build aus dem Monorepo-Root, curl-Diagnose, IONOS-Dateistruktur, externe `.env-logsink`, SQL-Splitting, Java-Client-Konfiguration und Code-Kommentierung.
- Dokument `docs/learning/13-from-unprotected-to-secure.md` als Sicherheits-Lernpfad von der offenen V1 zur geschützten API.
- Dokument `docs/learning/15-java-client-configuration-plan.md` zur geplanten Ablösung der hart codierten Java-Service-URL.
- Dokument `docs/learning/16-code-commenting-plan.md` als Plan für erklärende Kommentare im Service- und Client-Code.
- `database/mariadb/README.md` zur Nutzung der SQL-Skripte lokal und bei IONOS.
- IONOS-Minimaldeployment wurde dokumentiert: PHP 8.4.20, PDO MySQL, externe `.env-logsink`, erreichbarer V1-Endpunkt und erfolgreiche DB-Verbindung.

### Changed

- Service-Konfiguration kann über externe `.env-logsink` außerhalb des Service-Verzeichnisses geladen werden.
- MariaDB-Skript wurde in lokale Datenbankerzeugung, Schema für bestehende Datenbank und Demo-Daten getrennt.
- Temporäres PHP-Diagnose-Skript wurde aus dem Service-Kontext in `tools/diagnostics` verschoben.
- `docs/learning/README.md` wurde als vollständiger Index für alle Learning-Dokumente aktualisiert.
- `services/log-sink/README.md` wurde um IONOS-Testbetrieb, externe Konfiguration und Diagnosehinweise ergänzt.
- `TODO.md` wurde neu priorisiert.

### Security

- Öffentlich erreichbare `.env` im IONOS-Service-Verzeichnis wurde entfernt.
- Webchecks für `.env`, `_.env` und `.env-logsink` wurden dokumentiert.
- Diagnose-Skript darf nicht dauerhaft auf dem Server liegen.
- Die V1 bleibt weiterhin ungeschützt; geschützt wurde bisher vor allem die Konfigurationsablage, nicht die HTTP-API selbst.

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

# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format orientiert sich lose an Keep a Changelog, ohne bereits eine formale Release-Policy vorauszusetzen.

## [Unreleased]

### Added

- Ausführliches Dokument `docs/learning/11-v1-code-walkthrough-and-first-hardening.md` zur aktuellen funktionierenden V1, SQL, PHP-Service, Java-Viewer und erster Sicherungsmaßnahme.
- Plan `docs/learning/12-logging-client-plan.md` für schreibende Logging-Clients und deren Tests.
- Ergänzung im Sicherheits-Lernpfad, dass die erste Sicherungsmaßnahme die Verlagerung der echten `.env` aus der Browser-Reichweite war.
- MariaDB-README mit Erklärung der SQL-Dateien, Trigger, View und lokaler/IONOS-Nutzung.

### Changed

- `10-from-unprotected-to-secure.md` ist das kanonische Sicherheits-Lerndokument.
- `13-from-unprotected-to-secure.md` wird entfernt, um die doppelte Dokumentation zu vermeiden.
- `docs/learning/README.md` wurde an die bereinigte Dokumentstruktur angepasst.
- `docs/learning/06-session-state.md` wurde auf Dienstagmorgen aktualisiert.
- `docs/learning/99-open-questions.md` wurde um Fragen zu schreibenden Logging-Clients ergänzt.
- `docs/learning/05-decision-log.md` wurde um Entscheidungen zu Dokumentbereinigung und Logging-Clients ergänzt.
- `TODO.md` wurde auf die nächsten Schritte Dokumentation, Code-Kommentierung, Client-Konfiguration und Sender-Clients ausgerichtet.

### Security

- Der funktionierende IONOS-Stand wird als erster abgesicherter Zwischenstand dokumentiert: Die HTTP-API ist weiterhin ungeschützt, aber die echte Konfigurationsdatei liegt nicht mehr im öffentlich erreichbaren Service-Verzeichnis.

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

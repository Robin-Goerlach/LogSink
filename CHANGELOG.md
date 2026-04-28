# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format orientiert sich lose an Keep a Changelog, ohne bereits eine formale Release-Policy vorauszusetzen.

## [Unreleased]

### Added

- Erste curl-Beispiele für schreibende Log-Clients unter `examples/log-senders/curl`.
- curl-Reader-Beispiel unter `examples/log-readers/curl` zur schnellen Verifikation.
- Roundtrip-Smoke-Test für den aktuellen V0/V1-Flow: Sender -> Service -> Datenbank -> GET-API.
- Beispiel-Dokumentation unter `examples/`.
- Eigene EX-Nummerierung für Beispiele, damit keine Konflikte mit LS-Schritten aus dem Haupt-Lehrplan entstehen.

### Changed

- `docs/learning/12-logging-client-plan.md` verwendet jetzt EX-Nummern statt kollidierender LS-Nummern.
- `TODO.md` unterscheidet zwischen LS-Schritten für den Service und EX-Schritten für Beispiele.

### Security

- Die curl-Beispiele nutzen noch die bewusst ungeschützte V0/V1-API.
- Spätere EX-Schritte müssen Bearer-Token und Source-Scopes ergänzen.

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

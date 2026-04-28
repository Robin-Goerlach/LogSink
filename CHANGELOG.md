# Changelog

Alle nennenswerten Änderungen an diesem Projekt werden in dieser Datei dokumentiert.

Das Format orientiert sich lose an Keep a Changelog. Eine formale Release-Policy gibt es noch nicht.

## [Unreleased]

### Added

- Ausführliches Dokument `docs/learning/11-v1-code-walkthrough-and-first-hardening.md` zur aktuellen funktionierenden V1, SQL, PHP-Service, Java-Viewer und erster Sicherungsmaßnahme.
- Plan `docs/learning/12-logging-client-plan.md` für schreibende Logging-Clients, Reader-Beispiele und Roundtrip-Tests.
- Erste curl-Beispiele unter `examples/log-senders/curl`.
- curl-Reader unter `examples/log-readers/curl`.
- PHP-Logging-Client unter `examples/log-senders/php`.
- PHP-Reader unter `examples/log-readers/php`.
- Java-Logging-Client unter `examples/log-senders/java`.
- C#/.NET-Beispielprojekt unter `examples/csharp` mit Sender-, Reader- und Roundtrip-Befehlen.
- Roundtrip-Smoke-Tests für curl, PHP, Java und C#.
- MariaDB-README mit Erklärung der SQL-Dateien, Trigger, View und lokaler/IONOS-Nutzung.
- Eigene EX-Nummerierung für Beispiel-Clients, damit keine Konflikte mit LS-Schritten aus dem Hauptlehrplan entstehen.

### Changed

- `10-from-unprotected-to-secure.md` ist das kanonische Sicherheits-Lerndokument.
- `13-from-unprotected-to-secure.md` wurde entfernt, um doppelte Dokumentation zu vermeiden.
- `docs/learning/README.md` wurde an die bereinigte Dokumentstruktur angepasst.
- `docs/learning/06-session-state.md` wurde auf den aktuellen Arbeitsstand aktualisiert.
- `docs/learning/99-open-questions.md` wurde um Fragen zu schreibenden Logging-Clients ergänzt.
- `docs/learning/05-decision-log.md` wurde um Entscheidungen zu Dokumentbereinigung und Logging-Clients ergänzt.
- `docs/learning/12-logging-client-plan.md` beschreibt jetzt curl, PHP, Java und C# sowie die Rolle der Reader- und Roundtrip-Beispiele.
- `contracts/http-api/logs-v1.md` beschreibt nun den realen aktuellen V0/V1-Betrieb über `index.php` und grenzt ihn von der geplanten Ziel-API ab.
- Der Java-Viewer nutzt eine Konfigurationsdatei statt einer hart codierten Service-URL.
- Die bestehenden PHP- und Java-Dateien wurden ausführlicher kommentiert, damit der Code als Lernmaterial besser lesbar ist.

### Security

- Der funktionierende IONOS-Stand wird als erster abgesicherter Zwischenstand dokumentiert: Die HTTP-API ist weiterhin ungeschützt, aber die echte Konfigurationsdatei liegt nicht mehr im öffentlich erreichbaren Service-Verzeichnis.
- `.env` und `.env-logsink` dürfen nicht aus dem Browser abrufbar sein.
- Die Beispiel-Clients nutzen bewusst noch die ungeschützte V0/V1-API. Spätere Schritte müssen Bearer-Token, Source-Principal und Scopes ergänzen.
- Demo-Logmeldungen dürfen langfristig keine sensiblen lokalen Dateipfade, Zugangsdaten oder Tokens enthalten.

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

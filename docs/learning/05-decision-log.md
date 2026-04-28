# Decision Log

Diese Datei dokumentiert Architekturentscheidungen. Sie ist bewusst knapp gehalten.

## ADR-001: LogSink wird als Monorepo geführt

**Status:** Accepted

### Entscheidung

Service, Clients, Datenbank, API-Verträge, Skripte und Dokumentation bleiben in einem gemeinsamen Repository.

### Grund

Das Projekt ist ein Lehrprojekt. Datenbank, Service und Client sollen gemeinsam Schritt für Schritt weiterentwickelt werden.

### Konsequenzen

- PHP-Service liegt unter `services/log-sink`.
- Java-Client liegt unter `clients/java-log-viewer`.
- spätere Clients können unter `clients/` oder als Beispiele unter `examples/` ergänzt werden.
- spätere Services können unter `services/` ergänzt werden.

---

## ADR-002: Die ungeschützte V1 bleibt als Lern-Ausgangspunkt erhalten

**Status:** Accepted

### Entscheidung

Die aktuelle ungeschützte V1 wird nicht als Fehler betrachtet, sondern als didaktischer Ausgangspunkt.

### Grund

Sie beweist den Grundfluss von Client über HTTP-Service zur Datenbank und zurück zum Viewer. Sicherheit wird danach Schritt für Schritt ergänzt.

### Konsequenzen

- Sicherheitslücken werden dokumentiert.
- Neue Sicherheitsfunktionen werden atomar eingeführt.
- Legacy-Verhalten wird erst nach stabiler Ziel-API entfernt.

---

## ADR-003: Ziel-API orientiert sich an `/index.php?route=/api/v1/...`

**Status:** Accepted

### Entscheidung

Die Ziel-API unterstützt den Front-Controller-Zugriff über Query-Parameter `route`.

### Grund

Das Zielsystem soll ohne `.htaccess`, ohne URL-Rewriting, ohne Docker-Pflicht und shared-hosting-freundlich funktionieren.

### Konsequenzen

- `APP_ROUTE_PARAM` wird später Teil der Konfiguration.
- Java-Client muss Base-URL inklusive `index.php` unterstützen.
- Routen werden intern sauber gemappt.

---

## ADR-004: Lese- und Schreibidentitäten werden getrennt

**Status:** Accepted

### Entscheidung

Es gibt getrennte Principal-Typen:

- `source` für Ingest,
- `client` für Read.

### Grund

Ein Leser darf nicht automatisch schreiben. Eine Logquelle darf nicht automatisch lesen.

### Konsequenzen

- mindestens zwei Token-Arten.
- Java-Viewer braucht `readToken`.
- schreibende Beispiel-Clients brauchen `ingestToken`.
- Test-Log-Versand im Viewer darf nur mit bewusst konfiguriertem Source-/Ingest-Token erfolgen.

---

## ADR-005: Datenbankzugriff erfolgt später über Read-/Write-Trennung

**Status:** Proposed

### Entscheidung

Der Zielservice nutzt getrennte Datenbankbenutzer für Lesen und Schreiben.

### Grund

Least Privilege.

### Konsequenzen

- `.env-logsink` erhält später `DB_READ_*` und `DB_WRITE_*`.
- Repositories müssen bewusst den passenden DB-Zugang verwenden.
- Deployment-Doku muss DB-Rechte erklären.

---

## ADR-006: Composer wird eingeführt, aber nicht als erster Schritt

**Status:** Proposed

### Entscheidung

Composer kommt nach der API-Basis.

### Grund

Der aktuelle Service läuft ohne Composer. Vor Tooling sollen aktueller Start, curl-Tests und Ziel-API verstanden sein.

### Konsequenzen

- Phase 1/2 bleiben nah am aktuellen Code.
- Phase 3 führt Composer, PSR-4 und PHPUnit ein.

---

## ADR-007: Produktive/testweise IONOS-Konfiguration liegt außerhalb des Service-Verzeichnisses

**Status:** Accepted

### Entscheidung

Für den IONOS-Testbetrieb wird die echte Konfiguration nicht als `logsink/.env`, sondern als `.env-logsink` außerhalb des Service-Verzeichnisses abgelegt.

### Grund

Eine `.env` im öffentlich erreichbaren Webverzeichnis kann versehentlich per HTTP ausgeliefert werden. Das ist ein schweres Sicherheitsrisiko.

### Konsequenzen

- `Bootstrap::resolveEnvFile()` sucht externe Konfigurationspfade.
- lokale Entwicklung mit `services/log-sink/.env` bleibt möglich.
- Deployment-Dokumentation muss die Lage der `.env-logsink` erklären.

---

## ADR-008: Das Diagnose-Skript bleibt ein Tool, kein Service-Bestandteil

**Status:** Accepted

### Entscheidung

Das PHP-Diagnose-Skript liegt unter `tools/diagnostics/php-diagnose.php` und nicht im Service-Verzeichnis.

### Grund

Das Skript ist für temporäre Fehlersuche nützlich, gibt aber technische Betriebsinformationen aus. Es darf nicht dauerhaft online liegen.

### Konsequenzen

- Nur temporär auf den Server kopieren.
- Nach Diagnose sofort löschen.
- Server mit curl auf 404 prüfen.

---

## ADR-009: `10-from-unprotected-to-secure.md` ist das kanonische Sicherheits-Lerndokument

**Status:** Accepted

### Entscheidung

Das doppelte Dokument `13-from-unprotected-to-secure.md` wird entfernt. Der Sicherheits-Lernpfad liegt unter:

```text
docs/learning/10-from-unprotected-to-secure.md
```

### Grund

Zwei gleichnamige Dokumente mit unterschiedlicher Nummerierung verwirren und führen zu Pflegeaufwand.

### Konsequenzen

- README verweist nur noch auf `10-from-unprotected-to-secure.md`.
- neue Sicherheitsabschnitte werden dort ergänzt.

---

## ADR-010: Schreibende Logging-Clients werden als eigener Lern- und Testbereich geplant

**Status:** Accepted

### Entscheidung

Neben dem Java-Viewer werden schreibende Beispiel-Clients geplant.

### Grund

Ein Logging-Service wird nicht nur gelesen, sondern vor allem von anderen Programmen beschrieben. Sender-Clients sind gleichzeitig Dokumentation und Testwerkzeug.

### Konsequenzen

- neue LS-Schritte LS-021 bis LS-026.
- mögliche Struktur unter `examples/`.
- Tests prüfen später den Gesamtfluss: Sender -> Service -> DB -> Viewer.

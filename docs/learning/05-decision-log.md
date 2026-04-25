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
- spätere Clients können unter `clients/` ergänzt werden.
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

- `source` für Ingest
- `client` für Read

### Grund

Ein Leser darf nicht automatisch schreiben. Eine Logquelle darf nicht automatisch lesen.

### Konsequenzen

- mindestens zwei Token-Arten.
- Java-Client braucht `readToken` und optional `ingestToken`.
- Test-Log-Versand darf deaktivierbar sein, wenn kein Source-Token vorhanden ist.

---

## ADR-005: Datenbankzugriff erfolgt später über Read-/Write-Trennung

**Status:** Proposed

### Entscheidung

Der Zielservice nutzt getrennte Datenbankbenutzer für Lesen und Schreiben.

### Grund

Least Privilege.

### Konsequenzen

- `.env` erhält `DB_READ_USER`, `DB_READ_PASS`, `DB_WRITE_USER`, `DB_WRITE_PASS`.
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

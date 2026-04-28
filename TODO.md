# TODO - SASD LogSink

Diese Datei enthält konkrete Arbeitsaufgaben. Die Begründung und der rote Faden stehen in `docs/learning/`.

## Sofort / Dienstag

- [X] curl-Logbeispiele einspielen.
- [X] Remote-Service prüfen: `curl -i "http://api.sasd.de/logsink/index.php?limit=5"`.
- [X] Roundtrip-Smoke-Test ausführen.
- [X] Java-Viewer starten und prüfen, ob gesendete Meldungen sichtbar sind.
- [ ] API-Vertrag historisieren: aktueller `index.php`-Betrieb vs. spätere `/api`-/`route`-API.

## Erledigt am 2026-04-28

- [x] Doppelte Sicherheitsdokumentation bereinigt.
- [x] `10-from-unprotected-to-secure.md` als kanonisches Sicherheitsdokument festgelegt.
- [x] `11-v1-code-walkthrough-and-first-hardening.md` ergänzt.
- [x] `12-logging-client-plan.md` ergänzt.
- [x] Code von PHP-Service und Java-Viewer ausführlich kommentiert.
- [x] Editor-Swap-Datei entfernt und Swap-/Backup-Dateien ignoriert.
- [x] Java-Viewer-Konfiguration eingeführt.
- [x] `client-settings.example.json` eingeführt.
- [x] lokale `client-settings.json` ignoriert.
- [x] Hart codierte Service-URL aus `LogViewerFrame` entfernt.

## Examples

- [x] EX-001: Struktur für Beispiele festlegen.
- [x] EX-002: curl-Sender für JSON/Text/Error erstellen.
- [x] EX-003: curl-Reader zur Verifikation erstellen.
- [x] EX-004: Roundtrip-Smoke-Test erstellen.
- [ ] EX-005: PHP-Logging-Client erstellen.
- [ ] EX-006: Java-Logging-Client erstellen.
- [ ] EX-007: Sender-Clients später an Authentifizierung anpassen.

## Service

- [ ] LS-021: Request-ID einführen.
- [ ] LS-022: Einheitliches JSON-Antwortmodell einführen.
- [ ] LS-023: Einfaches Routing einführen.
- [ ] LS-024: Front-Controller-Route-Parameter ergänzen.
- [ ] Health-Endpunkt `/api/v1/health` ergänzen.
- [ ] JSON-Body-Regeln durchsetzen: Content-Type, Maximalgröße, JSON-Objekt als Root.
- [ ] Ingest-Endpunkt `/api/v1/ingest/events` für strukturierte Events einführen.
- [ ] Bearer-Token-Authentifizierung vorbereiten.
- [ ] Principal-Typen `source` und `client` trennen.
- [ ] Scope-Prüfung für `events.ingest`, `events.read`, `sources.read` einführen.

## Datenbank

- [ ] Bestehende Tabelle `log_entries` als Legacy/V0 einordnen.
- [ ] Neue Tabellen für strukturierte Logereignisse planen.
- [ ] `ingest_requests` ergänzen.
- [ ] `log_events` ergänzen.
- [ ] `access_audit` ergänzen.
- [ ] Credential-/Token-Tabellen für Sources und Clients ergänzen.

## Java-Viewer

- [x] Hart codierte Service-URL entfernen.
- [x] `client-settings.example.json` einführen.
- [x] `client-settings.json` in `.gitignore` aufnehmen.
- [x] Settings-Loader ergänzen.
- [ ] Route-Parameter und API-Version konfigurierbar machen.
- [ ] Bearer-Token für Lesezugriffe unterstützen.
- [ ] Health-Check-Schaltfläche ergänzen.
- [ ] Eventliste auf neues Response-Modell `ok/requestId/data` umstellen.

## Dokumentation

- [ ] `contracts/http-api/logs-v1.md` an aktuellen V0/V1-Betrieb anpassen.
- [ ] `docs/learning/10-git-workflows.md` erstellen.
- [ ] phpDocumentor-Kapitel ergänzen, sobald Composer eingeführt ist.

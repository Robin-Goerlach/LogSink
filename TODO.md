# TODO - SASD LogSink

Diese Datei enthält konkrete Arbeitsaufgaben. Die Begründung und der rote Faden stehen in `docs/learning/`.

## Sofort / Dienstag

- [ ] `README_LEARNING_DOCS.md` aktualisieren.
- [ ] `git status` prüfen.
- [ ] Remote-Service prüfen: `curl -i "http://api.sasd.de/logsink/index.php?limit=5"`.
- [ ] Sicherheitschecks wiederholen:
  - [ ] `curl -i "http://api.sasd.de/logsink/.env"`
  - [ ] `curl -i "http://api.sasd.de/logsink/_.env"`
  - [ ] `curl -i "http://api.sasd.de/.env-logsink"`

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

## Next

- [ ] LS-021: Schreibende Beispiel-Clients final platzieren: `examples/` oder `clients/`.
- [ ] LS-022: curl-Logging-Beispiele erstellen.
- [ ] LS-023: PHP-Logging-Client erstellen.
- [ ] LS-024: Java-Logging-Client erstellen.
- [ ] LS-025: Sender-Clients in Testplan aufnehmen.
- [ ] LS-026: Sender-Clients später an Authentifizierung anpassen.
- [ ] `docs/learning/06-session-state.md` am Ende der Sitzung aktualisieren.

## Service

- [ ] Health-Endpunkt `/api/v1/health` ergänzen.
- [ ] Front-Controller-Routing über `index.php?route=/api/v1/...` einführen.
- [ ] Einheitliches JSON-Antwortmodell mit `ok`, `requestId`, `data` bzw. `error` einführen.
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

## Schreibende Logging-Clients

- [ ] Ordnerstruktur für Sender-Beispiele festlegen.
- [ ] curl-Sender für aktuelle ungeschützte V1.
- [ ] PHP-Sender für aktuelle ungeschützte V1.
- [ ] Java-Sender für aktuelle ungeschützte V1.
- [ ] Gemeinsame Beispiel-Logmeldung definieren.
- [ ] Tests definieren: Sender -> Service -> DB -> Viewer.
- [ ] Spätere Token-Variante vorbereiten.

## Dokumentation

- [ ] `docs/learning/10-git-workflows.md` erstellen.
- [ ] phpDocumentor-Kapitel ergänzen, sobald Composer eingeführt ist.

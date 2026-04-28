# TODO - SASD LogSink

Diese Datei enthält konkrete Arbeitsaufgaben. Die Begründung und der rote Faden stehen in `docs/learning/`.

## Sofort / Dienstag

- [ ] Doppelte Sicherheitsdokumentation bereinigen: `docs/learning/13-from-unprotected-to-secure.md` entfernen.
- [ ] Dokumentationspaket vom 2026-04-28 einspielen.
- [ ] `git status` prüfen.
- [ ] Remote-Service prüfen: `curl -i "http://api.sasd.de/logsink/index.php?limit=5"`.
- [ ] Sicherheitschecks wiederholen:
  - [ ] `curl -i "http://api.sasd.de/logsink/.env"`
  - [ ] `curl -i "http://api.sasd.de/logsink/_.env"`
  - [ ] `curl -i "http://api.sasd.de/.env-logsink"`

## Next

- [ ] LS-014 / LS-020: Code stärker kommentieren.
- [ ] LS-015 / LS-019: Java-Client-Konfiguration einführen.
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
- [ ] Optionalen Token-Pepper in `.env-logsink` berücksichtigen.
- [ ] File-basiertes Rate-Limiting einführen.
- [ ] IP-Allowlisting für Sources einführen.
- [ ] Audit-Logging in Datenbank ergänzen.
- [ ] Defensive Fehlerausgabe ohne Stacktraces oder SQL-Details absichern.

## Datenbank

- [ ] Bestehende Tabelle `log_entries` als Legacy/V0 einordnen.
- [ ] Neue Tabellen für strukturierte Logereignisse planen.
- [ ] `ingest_requests` ergänzen.
- [ ] `log_events` ergänzen.
- [ ] `access_audit` ergänzen.
- [ ] `log_source_networks` ergänzen.
- [ ] Credential-/Token-Tabellen für Sources und Clients ergänzen.
- [ ] Views `api_v1_log_events` und `api_v1_log_sources` ergänzen.
- [ ] Separate Read-/Write-Datenbankbenutzer einführen.
- [ ] Seed-/Provisioning-Skripte für Demo-Source und Demo-Client vorbereiten.

## Java-Viewer

- [ ] Hart codierte Service-URL entfernen.
- [ ] `client-settings.example.json` einführen.
- [ ] `client-settings.json` in `.gitignore` aufnehmen.
- [ ] Settings-Loader ergänzen.
- [ ] Route-Parameter und API-Version konfigurierbar machen.
- [ ] Bearer-Token für Lesezugriffe unterstützen.
- [ ] Separaten Source-/Ingest-Token für Test-Log-Versand unterstützen oder Funktion deaktivierbar machen.
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
- [ ] README des Java-Viewers an neue Konfiguration anpassen.
- [ ] phpDocumentor-Kapitel ergänzen, sobald Composer eingeführt ist.

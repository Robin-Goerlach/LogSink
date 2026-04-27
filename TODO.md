# TODO - SASD LogSink

Diese Datei enthält konkrete Arbeitsaufgaben. Die Begründung und der rote Faden stehen in `docs/learning/`.

## Sofort

- [ ] Gelöschtes Diagnose-Skript im Service-Verzeichnis committen: `git add -u services/log-sink/php-diagnose.php`.
- [ ] Prüfen, ob `tools/diagnostics/php-diagnose.php` besser in `php-diagnose.php.example` umbenannt werden soll.
- [ ] Prüfen, ob `.env.IONOS` sauber ignoriert wird.
- [ ] Remote-Service erneut testen: `curl -i "http://api.sasd.de/logsink/index.php?limit=5"`.
- [ ] Sicherheitschecks wiederholen:
  - [ ] `curl -i "http://api.sasd.de/logsink/.env"`
  - [ ] `curl -i "http://api.sasd.de/logsink/_.env"`
  - [ ] `curl -i "http://api.sasd.de/.env-logsink"`

## Next

- [ ] LS-014: Code stärker kommentieren.
- [ ] LS-015/LS-019: Java-Client-Konfiguration einführen.
- [ ] LS-016: IONOS-/Deployment-Dokumentation ausarbeiten.
- [ ] LS-017: MariaDB-Skripte für lokal und IONOS dokumentieren.
- [ ] LS-018: Dokument "Von ungeschützt zu sicher" weiter ausarbeiten.
- [ ] `docs/learning/06-session-state.md` am Ende der nächsten Sitzung aktualisieren.

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

- [ ] `database/mariadb/README.md` ergänzen.
- [ ] Nutzung der SQL-Dateien lokal und bei IONOS dokumentieren.
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

## Java-Client

- [ ] Hart codierte Service-URL entfernen.
- [ ] `client-settings.example.json` einführen.
- [ ] `client-settings.json` in `.gitignore` aufnehmen.
- [ ] Settings-Loader ergänzen.
- [ ] Route-Parameter und API-Version konfigurierbar machen.
- [ ] Bearer-Token für Lesezugriffe unterstützen.
- [ ] Separaten Source-/Ingest-Token für Test-Log-Versand unterstützen oder Funktion deaktivierbar machen.
- [ ] Health-Check-Schaltfläche ergänzen.
- [ ] Eventliste auf neues Response-Modell `ok/requestId/data` umstellen.
- [ ] Paging mit `page`, `pageSize`, `total` anzeigen.
- [ ] Serverfilter für `sourceKey`, `severityText`, `traceId`, `correlationId`, `from`, `to` ergänzen.
- [ ] Detailsicht über `/api/v1/events/{eventId}` ergänzen.
- [ ] Related-Events-Suche über `traceId` und `correlationId` ergänzen.
- [ ] Quellenübersicht über `/api/v1/sources` ergänzen.
- [ ] CSV- und JSON-Export ergänzen.
- [ ] Lokales Diagnose-Logging ohne Token-Leaks ergänzen.
- [ ] Fehlerdialoge für 401, 403, 429, 413, 415, 422 verbessern.

## Dokumentation

- [ ] `docs/learning/10-git-workflows.md` erstellen.
- [ ] `CHANGELOG.md` bei relevanten Projektänderungen fortschreiben.
- [ ] README des PHP-Service an neue Start-/Testbefehle anpassen.
- [ ] README des Java-Clients an neue Konfiguration anpassen.
- [ ] IONOS-Deployment-Kapitel konkretisieren.
- [ ] phpDocumentor-Kapitel ergänzen, sobald Composer eingeführt ist.

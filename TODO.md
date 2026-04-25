# TODO - SASD LogSink

Diese Datei enthält nur konkrete Arbeitsaufgaben. Die Begründung und der rote Faden stehen in `docs/learning/`.

## Next

- [ ] Bereitgestellte Mustralla/MustelaLogAPI-Dokumente gegen die aktuelle LogSink-V1 abgleichen.
- [ ] API-Vertrag V1.1 in `contracts/http-api/logs-v1.md` oder neuer `logs-v1.1.md` vorbereiten.
- [ ] Entscheidung treffen: API-Kompatibilitätsschicht für alte `/api/logs` behalten oder früh entfernen.
- [ ] Composer im PHP-Service einführen.
- [ ] PHP-Autoloading sauber auf PSR-4 umstellen.
- [ ] Erste PHPUnit-Infrastruktur einrichten.
- [ ] curl-Smoke-Tests in scripts erweitern.
- [ ] MariaDB-Schema in migrationsartige Dateien zerlegen.
- [ ] Tabellen für Tenants, Sources, Clients/Credentials, Scopes und Audit entwerfen.
- [ ] Java-Client-Konfiguration von URL/Limit auf vollständige Client-Konfiguration ausbauen.

## Service

- [ ] Health-Endpunkt `/api/v1/health` ergänzen.
- [ ] Front-Controller-Routing über `index.php?route=/api/v1/...` einführen.
- [ ] Einheitliches JSON-Antwortmodell mit `ok`, `requestId`, `data` bzw. `error` einführen.
- [ ] JSON-Body-Regeln durchsetzen: Content-Type, Maximalgröße, JSON-Objekt als Root.
- [ ] Ingest-Endpunkt `/api/v1/ingest/events` für strukturierte Events einführen.
- [ ] Bearer-Token-Authentifizierung vorbereiten.
- [ ] Principal-Typen `source` und `client` trennen.
- [ ] Scope-Prüfung für `events.ingest`, `events.read`, `sources.read` einführen.
- [ ] Optionalen Token-Pepper in `.env` berücksichtigen.
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

## Java-Client

- [ ] `client-settings.example.json` einführen.
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

- [ ] `docs/learning/06-session-state.md` am Ende jeder Arbeitssitzung aktualisieren.
- [ ] `docs/learning/05-decision-log.md` bei Architekturentscheidungen ergänzen.
- [ ] `CHANGELOG.md` bei relevanten Projektänderungen fortschreiben.
- [ ] README des PHP-Service an neue Start-/Testbefehle anpassen.
- [ ] README des Java-Clients an neue Konfiguration anpassen.
- [ ] IONOS-Deployment-Kapitel konkretisieren, sobald Hosting-Variante feststeht.
- [ ] phpDocumentor-Kapitel ergänzen, sobald Composer eingeführt ist.

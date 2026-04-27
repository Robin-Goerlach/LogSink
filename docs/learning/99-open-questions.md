# Open Questions

## API und Kompatibilität

1. Soll `/api/logs` langfristig erhalten bleiben?
2. Soll die Ziel-API in `logs-v1.md` oder neuer Datei `logs-v1.1.md` dokumentiert werden?
3. Wann wird der Java-Client endgültig auf `/api/v1/...` umgestellt?
4. Soll der Service zeitweise beide Response-Modelle liefern können?

## Datenbank

1. Wie heißen die finalen Credential-Tabellen?
2. Wird `tenant` schon in V1 benötigt oder erst als vorbereitendes Feld?
3. Sollen Source- und Client-Credentials in einer gemeinsamen Tabelle oder getrennten Tabellen liegen?
4. Wie wird Token-Provisionierung als Demo-Skript gelöst?
5. Bleibt `log_entries` dauerhaft als Legacy-Tabelle erhalten?
6. Sollen die SQL-Dateien später zusätzlich als Migrationen mit Versionsnummern geführt werden?

## Sicherheit

1. Wann wird `AUTH_TOKEN_PEPPER` eingeführt?
2. Wie werden Demo-Tokens für lokale Tests erzeugt?
3. Wie werden Tokens im Java-Client gespeichert?
4. Soll der Client Tokens maskiert anzeigen können?
5. Soll IP-Allowlisting in V1 zwingend aktiv sein oder optional?
6. Wann wird `APP_DEBUG=false` für den IONOS-Testbetrieb verwendet?

## Client

1. Soll der Java-Client Test-Logs senden können oder zunächst nur lesen?
2. Soll der Java-Client später Saved Views speichern?
3. Wird ein WPF-Client im selben Repository ergänzt?
4. Soll der Java-Client automatische Refreshes unterstützen?
5. Welche Exportfelder sind für CSV/JSON verbindlich?
6. Soll der Java-Client kurzfristig Legacy-API und Ziel-API parallel unterstützen?

## Betrieb

1. Bleibt IONOS Shared Hosting das primäre Testziel?
2. Gibt es SSH/Composer auf dem Zielhosting?
3. Kann DocumentRoot später direkt auf `public/` zeigen?
4. Wo liegt `.env-logsink` im endgültigen Betrieb?
5. Wie werden Schreibrechte für `var/log` und später `var/cache/rate-limit` gesetzt?
6. Soll das Diagnose-Skript langfristig `.example` heißen?

## Dokumentation

1. Soll zusätzlich ein Word/PDF-Lehrplan erzeugt werden?
2. Soll `docs/learning` später in eine offizielle Projektdokumentation überführt werden?
3. Soll phpDocumentor als CI-Schritt vorbereitet werden?
4. Soll `docs/learning/10-git-workflows.md` als nächstes Dokument ergänzt werden?

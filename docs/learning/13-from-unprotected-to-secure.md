# Von ungeschützt zu sicher - Die Sicherheitsentwicklung von SASD LogSink

## Zweck

Dieses Dokument begleitet den Weg von der bewusst ungeschützten V1 zu einem sicheren Logging-Service.

Es ist ein Lerntext: Er erklärt, warum wir eine Schutzschicht einführen, was sie leistet, welche Grenzen sie hat und wie wir prüfen, ob sie funktioniert.

## Ausgangspunkt: die bewusst ungeschützte V1

Die erste Version von LogSink ist absichtlich einfach:

```text
GET  /api/logs?limit=100
POST /api/logs
```

Jeder erreichbare Client darf schreiben und lesen. Die Datenbank speichert den Request-Body unverändert.

## Warum war das sinnvoll?

Die ungeschützte V1 beweist zuerst den Grundfluss:

```text
Client -> HTTP -> PHP-Service -> MariaDB -> Java-Viewer
```

Damit sind die Grundbausteine sichtbar:

- HTTP-Request,
- PHP-Frontcontroller,
- `.env`-Konfiguration,
- Datenbankverbindung,
- Repository,
- JSON-Antwort,
- Java-Client,
- Maven-Build,
- curl-Test.

## Risiken der V1

| Risiko | Erklärung |
|---|---|
| Jeder kann schreiben | Ein Angreifer könnte die Datenbank fluten. |
| Jeder kann lesen | Logdaten können sensible Informationen enthalten. |
| Keine Payload-Grenze | Zu große Requests können Ressourcen belasten. |
| Keine Validierung | Beliebige Inhalte werden gespeichert. |
| Keine Authentifizierung | Der Service weiß nicht, wer anfragt. |
| Keine Autorisierung | Es gibt keine getrennten Rechte. |
| Keine Auditierung | Zugriffe werden nicht fachlich nachvollzogen. |
| Keine Rate-Limits | Missbrauch wird nicht gebremst. |

## Schutzschicht 1: sichere Konfiguration

### Problem

Die ursprüngliche `.env` lag im öffentlich erreichbaren Webverzeichnis.

### Lösung

Die echte Konfiguration wird als `.env-logsink` außerhalb des Service-Verzeichnisses abgelegt.

### Test

```bash
curl -i "http://api.sasd.de/logsink/.env"
curl -i "http://api.sasd.de/logsink/_.env"
curl -i "http://api.sasd.de/.env-logsink"
```

Erwartung: keine Secrets werden ausgeliefert.

### Status

Für IONOS-Minimalbetrieb erledigt.

## Schutzschicht 2: klare Routen

Ziel:

```text
GET  /index.php?route=/api/v1/health
POST /index.php?route=/api/v1/ingest/events
GET  /index.php?route=/api/v1/events
GET  /index.php?route=/api/v1/events/{eventId}
GET  /index.php?route=/api/v1/sources
```

Klare Routen ermöglichen getrennte Rechte, Validierung und Tests.

## Schutzschicht 3: standardisierte JSON-Antworten

Erfolg:

```json
{
  "ok": true,
  "requestId": "...",
  "data": {}
}
```

Fehler:

```json
{
  "ok": false,
  "error": {
    "code": "...",
    "message": "...",
    "requestId": "..."
  }
}
```

## Schutzschicht 4: JSON-Validierung

Der Service prüft:

- Content-Type,
- Body-Größe,
- gültiges JSON,
- JSON-Root als Objekt,
- Pflichtfelder,
- Feldtypen.

## Schutzschicht 5: Bearer-Token

Geschützte Endpunkte verlangen:

```http
Authorization: Bearer <token>
```

## Schutzschicht 6: Token-Hashing und Pepper

Tokens werden nicht im Klartext gespeichert. Die Datenbank enthält nur Hashes. Optional wird ein Pepper aus `.env-logsink` verwendet.

## Schutzschicht 7: Principal-Typen

| Principal | Aufgabe |
|---|---|
| `source` | Events schreiben |
| `client` | Events und Sources lesen |

## Schutzschicht 8: Scopes

| Scope | Bedeutung |
|---|---|
| `events.ingest` | Events schreiben |
| `events.read` | Events lesen |
| `sources.read` | Sources lesen |

## Schutzschicht 9: Datenbankrechte

Ziel: separate Datenbankbenutzer für Lesen und Schreiben.

## Schutzschicht 10: Audit

Sicherheitsrelevante Zugriffe werden in `access_audit` gespeichert.

## Schutzschicht 11: Rate-Limiting

Zu viele Requests werden gebremst.

## Schutzschicht 12: IP-Allowlisting für Sources

Ein Source-Token darf optional nur aus bestimmten Netzen schreiben.

## Schutzschicht 13: sichere Client-Konfiguration

Der Java-Client speichert später:

- Service-URL,
- Read-Token,
- optional Ingest-Token,
- Timeouts,
- Page-Size.

Tokens dürfen nicht in Logs, Exporte oder Fehlermeldungen gelangen.

## Fazit

Die V1 war absichtlich offen. Der Weg zur Sicherheit besteht aus nachvollziehbaren Schutzschichten. Jede Schicht muss ein konkretes Risiko reduzieren, testbar sein und dokumentiert werden.

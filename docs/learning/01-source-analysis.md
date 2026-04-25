# Source Analysis - bereitgestellte Mustralla/MustelaLogAPI-Dokumente

## Ausgewertete Dokumente

Die Analyse basiert auf den bereitgestellten PDF-Dokumenten:

- `Mustralla_LogAPI-Pflichtenheft.pdf`
- `Mustralla_LogAPI-ServerInterface.pdf`
- `Mustralla_LogAPI-ServerInterface-Test.pdf`
- `Mustralla_LogAPI-Lastenheft-Client.pdf`
- `Mustralla_LogAPI-Pflichtenheft-Client.pdf`
- `Mustralla_LogAPI-Server-Konfiguration.pdf`
- `Mustralla_LogAPI-Client-Konfiguration.pdf`

Die Dokumente beschreiben nicht exakt unseren aktuellen `LogSink`-Code, sondern ein Zielsystem mit ähnlicher Struktur und deutlich stärkerer Sicherheits- und Schnittstellendisziplin.

## Gemeinsames Zielbild

Das Zielsystem ist eine PHP-basierte HTTP/JSON-Middleware zwischen Logquellen, lesenden Clients und einer MySQL/MariaDB-Datenbank.

Wichtige Merkmale:

- keine direkten Datenbankzugriffe für externe Clients,
- strukturierte Events,
- Bearer-Token-Authentifizierung,
- Source-Principal für Schreibzugriffe,
- Client-Principal für Lesezugriffe,
- Scope-Prüfung,
- tenantbezogene Lesezugriffe,
- Views für lesende API-Pfade,
- Repositories für kontrollierten Datenbankzugriff,
- Audit-Trail,
- Rate-Limiting,
- sichere Fehlerantworten.

## Server-Zielbild

### Endpunkte

Das Zielbild kennt fünf zentrale V1-Endpunkte:

| Methode | Pfad | Authentisierung | Scope | Zweck |
|---|---|---|---|---|
| GET | `/api/v1/health` | nein | - | Healthcheck |
| POST | `/api/v1/ingest/events` | Bearer `source` | `events.ingest` | Events schreiben |
| GET | `/api/v1/events` | Bearer `client` | `events.read` | Eventliste lesen |
| GET | `/api/v1/events/{eventId}` | Bearer `client` | `events.read` | Einzelnes Event lesen |
| GET | `/api/v1/sources` | Bearer `client` | `sources.read` | Quellenliste lesen |

Der dokumentierte Shared-Hosting-freundliche Zugriff erfolgt bevorzugt über:

```text
/index.php?route=/api/v1/...
```

Das passt gut zu unserem Projektziel, weil wir keine `.htaccess` und keine verpflichtenden Rewrite-Regeln verwenden wollen.

### Request-/Response-Modell

Zielmodell für Erfolg:

```json
{
  "ok": true,
  "requestId": "<request-id>",
  "data": {}
}
```

Zielmodell für Fehler:

```json
{
  "ok": false,
  "error": {
    "code": "<error-code>",
    "message": "<public-message>",
    "requestId": "<request-id>"
  }
}
```

Wichtige Regel: Interne Details, Stacktraces und SQL-Fehler werden nicht an Clients ausgeliefert.

### Ingest

Der Ingest-Pfad nimmt strukturierte Events an. Sichtbare Kernfelder sind unter anderem:

- `occurredAt`
- `observedAt`
- `severityNumber`
- `severityText`
- `eventName`
- `eventCategory`
- `eventAction`
- `eventOutcome`
- `message`
- `hostName`
- `serviceName`
- `traceId`
- `correlationId`
- `attributes`

Wichtige Validierungsregeln:

- Body muss JSON sein.
- JSON-Root muss ein Objekt sein.
- `severityNumber` liegt im Bereich 1 bis 24.
- `severityText` ist Pflicht.
- `message` ist Pflicht.
- Zeitfelder werden normalisiert.
- Attribute sind Objekt oder Array.
- Einzel-Event und Batch über `events`-Array sollen möglich sein.
- Erfolgreicher Ingest liefert `202 Accepted`.

### Lesen

Für `/api/v1/events` sind mindestens diese Query-Parameter vorgesehen:

| Parameter | Bedeutung |
|---|---|
| `page` | Seitennummer, Default 1 |
| `pageSize` | Seitengröße, Default 50, Maximum 200 |
| `sourceKey` | Filter nach Quelle |
| `severityText` | Filter nach Severity |
| `traceId` | Filter nach Trace-ID |
| `correlationId` | Filter nach Correlation-ID |
| `from` | untere Zeitgrenze |
| `to` | obere Zeitgrenze |
| `sort` | Whitelist: `occurredAt`, `severityNumber`, `sourceKey` |
| `direction` | `ASC` oder `DESC` |

## Datenbank-Zielbild

Das Zielsystem benötigt mindestens diese fachlichen Bereiche:

- Tenants bzw. Mandantenzuordnung
- Sources
- Client-/Source-Credentials
- Scopes
- Ingest-Requests
- Logevents
- Zugriffsaudit
- erlaubte Quellnetze
- lesende API-Views

Sichtbar relevante Objekte aus den Dokumenten:

- `ingest_requests`
- `log_events`
- `access_audit`
- `log_source_networks`
- `api_v1_log_events`
- `api_v1_log_sources`

Zusätzlich benötigen wir eigene Tabellen für Credentials und Scope-Zuordnungen. Die bereitgestellten Dokumente markieren fehlende SQL-DDL bewusst als offene Grenze; für unser Projekt müssen wir diese DDL deshalb sauber selbst entwerfen.

## Sicherheits-Zielbild

### Authentifizierung

- HTTP-Header: `Authorization: Bearer <token>`
- Token werden nicht im Klartext in der Datenbank gespeichert.
- Vergleich über SHA-256-Hash.
- Optionaler Pepper aus Serverkonfiguration.
- Klartexttoken wird nur vom Client gesendet.

### Autorisierung

Principal-Typen:

| Principal | Zweck |
|---|---|
| `source` | darf Events schreiben |
| `client` | darf Events und Sources lesen |

Scopes:

| Scope | Zweck |
|---|---|
| `events.ingest` | Events schreiben |
| `events.read` | Events lesen |
| `sources.read` | Sources lesen |

### Weitere Schutzmechanismen

- IP-Allowlisting für Source-Principals
- file-basiertes Rate-Limiting
- Read-/Write-Trennung bei Datenbankbenutzern
- Audit-Logging
- defensive Fehlerantworten

## Client-Zielbild

Der Client ist keine Datenbankanwendung. Er spricht ausschließlich HTTP/JSON mit dem Service.

V1-Kern:

- Verbindungskonfiguration
- Healthcheck
- Bearer-Token für Lesen
- optional separater Source-Token für Test-Log-Versand
- Eventtabelle
- Paging
- Sortierung
- serverseitige Filter
- Detailansicht
- Related Events über `traceId`/`correlationId`
- Quellenübersicht
- Test-Log-Versand
- CSV-/JSON-Export
- lokales Diagnose-Logging
- verständliche Fehlerdialoge

## Erkannte Spannungen

### 1. Unser aktueller Service speichert Rohdaten, Zielservice strukturierte Events

Aktuell: `raw_message LONGBLOB`.

Ziel: validierte, normalisierte Events mit Fachfeldern, Hashkette und API-Views.

### 2. Unser aktueller Service ist ungeschützt, Zielservice ist rollen- und scopebasiert

Aktuell: jeder darf lesen und schreiben.

Ziel: `source` mit `events.ingest`; `client` mit `events.read`/`sources.read`.

### 3. Unser aktuelles Response-Modell ist anders

Aktuell:

```json
{
  "status": "created"
}
```

Ziel:

```json
{
  "ok": true,
  "requestId": "...",
  "data": {}
}
```

### 4. Unser Java-Client liest aktuell direkt das alte Format

Der Java-Client erwartet aktuell `status` und `items`. Er muss später auf `ok/requestId/data` umgestellt werden.

### 5. Ein-Token-Clients vs. getrennte Rollen

Die Dokumente benennen die Spannung, dass sichtbare Clients oft nur einen technischen Token kennen, der Service aber zwischen Lese-Client und Schreib-Source unterscheidet. Für LogSink lösen wir das didaktisch sauber durch getrennte Konfiguration:

- `readToken`
- `ingestToken`

oder durch deaktivierbaren Test-Log-Versand.

## Folgerung für LogSink

LogSink sollte nicht sofort vollständig umgebaut werden. Sinnvoll ist eine Übergangsstrategie:

1. Aktuellen V0/V1-Rohdatenservice lauffähig halten.
2. Standardisiertes Response-Modell einführen.
3. Neue `/api/v1/...`-Endpunkte parallel ergänzen.
4. Datenbank schrittweise erweitern.
5. Authentifizierung zuerst als klarer Lernschritt einführen.
6. Danach Scope, Tenant, Audit, Rate-Limit, IP-Allowlisting.
7. Java-Client in passenden Etappen auf das Zielmodell heben.

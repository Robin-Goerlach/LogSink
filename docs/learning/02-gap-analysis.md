# Gap Analysis - aktueller LogSink-Stand vs. Zielbild

## Zweck

Diese Gap-Analyse verhindert, dass wir unbemerkt zwischen dem aktuellen LogSink-Code und dem Zielbild der bereitgestellten Dokumente springen.

## Aktueller Stand

Aktuell besitzt LogSink:

| Bereich | Aktueller Stand |
|---|---|
| Service | einfacher PHP-Service |
| Route | Methodenbasiert: `POST` schreibt, `GET` liest |
| Schreibpfad | speichert Raw-Body unverändert |
| Lesepfad | liest letzte Logs aus View `v_log_entries` |
| Datenbank | `log_entries`, Trigger, View |
| Authentifizierung | keine |
| Autorisierung | keine |
| Client | Java Swing Tabelle mit GET |
| Tests | manuelle curl-Beispiele / Smoke-Test |
| Composer | noch nicht eingeführt |
| PHPUnit | noch nicht eingeführt |
| phpDocumentor | noch nicht eingeführt |

## Zielbild

| Bereich | Ziel |
|---|---|
| Service | PHP HTTP/JSON Middleware |
| Route | `/index.php?route=/api/v1/...` |
| Schreibpfad | `POST /api/v1/ingest/events` |
| Lesepfad | `/events`, `/events/{eventId}`, `/sources` |
| Datenbank | strukturierte Tabellen + Views + Audit |
| Authentifizierung | Bearer-Token |
| Autorisierung | Principal-Typ + Scope |
| Client | konfigurierbarer Desktop-Client |
| Tests | curl, PHPUnit, End-to-End, Java-Client-Tests |
| Composer | PSR-4, Autoloading, Dev-Tools |
| PHPUnit | Unit- und Integrationstests |
| phpDocumentor | API-/Code-Dokumentation |

## Gap 1: Routing

### Ist

```text
GET  /api/logs?limit=100
POST /api/logs
```

Der Code wertet aktuell im Wesentlichen die HTTP-Methode aus.

### Soll

```text
GET  /index.php?route=/api/v1/health
POST /index.php?route=/api/v1/ingest/events
GET  /index.php?route=/api/v1/events
GET  /index.php?route=/api/v1/events/{eventId}
GET  /index.php?route=/api/v1/sources
```

### Lehrplan-Folge

Zuerst eine kleine Router-Schicht einführen, ohne sofort Authentifizierung einzubauen.

## Gap 2: Response-Modell

### Ist

```json
{
  "status": "ok",
  "items": []
}
```

oder

```json
{
  "status": "created",
  "id": 1
}
```

### Soll

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

### Lehrplan-Folge

Als früher Schritt ein `JsonResponse`-Konzept und Request-ID einführen. Der Java-Client muss danach angepasst werden.

## Gap 3: Datenmodell

### Ist

```text
log_entries
- id
- received_at
- raw_message
- technische HTTP-Metadaten
- raw_message_size
- payload_sha256
```

### Soll

```text
ingest_requests
log_events
access_audit
log_source_networks
credential/source/client/scopes
api_v1_log_events
api_v1_log_sources
```

### Lehrplan-Folge

Die alte Tabelle zunächst nicht löschen. Sie bleibt als V0/Legacy erhalten. Das neue Schema wird daneben aufgebaut.

## Gap 4: Sicherheit

### Ist

Jeder kann lesen und schreiben.

### Soll

- Bearer-Token
- Token-Hash statt Klartext
- optionaler Pepper
- `source` vs. `client`
- `events.ingest`, `events.read`, `sources.read`
- IP-Allowlisting
- Rate-Limiting
- Audit

### Lehrplan-Folge

Nicht alles gleichzeitig.

Reihenfolge:

1. Request-ID und Fehlerformat.
2. Bearer-Token-Grundmechanik.
3. Token-Hashing.
4. Principal-Typen.
5. Scopes.
6. DB-gestützte Credentials.
7. Audit.
8. IP-Allowlist.
9. Rate-Limit.

## Gap 5: Client-Konfiguration

### Ist

Der Java-Client hat URL und Limit im Fenster.

### Soll

Lokale Konfiguration mit mindestens:

- Basis-URL
- Route-Parameter
- API-Version
- Timeout
- Standard-Page-Size
- Client-/Read-Token
- optional Source-/Ingest-Token
- Diagnose-Log
- UI-Präferenzen

### Lehrplan-Folge

Zuerst `client-settings.example.json` und `client-settings.json` einführen. Danach UI-Felder schrittweise an diese Konfiguration binden.

## Gap 6: Ingest Payload

### Ist

Der Service speichert alles unverändert.

### Soll

Der Service erwartet strukturierte JSON-Events.

Mindestfelder:

- `occurredAt`
- `observedAt`
- `severityNumber`
- `severityText`
- `message`

Weitere wichtige Felder:

- `eventName`
- `eventCategory`
- `eventAction`
- `eventOutcome`
- `hostName`
- `serviceName`
- `traceId`
- `correlationId`
- `attributes`

### Lehrplan-Folge

Zuerst akzeptieren wir ein sehr kleines strukturiertes Event. Danach erweitern wir Validierung und Felder.

## Gap 7: Tests

### Ist

Manuelle curl-Beispiele.

### Soll

- Health-Smoke-Test
- Auth-Negativtests
- Ingest-Erfolgstest
- Ingest-Validierungsfehler
- Eventlisten-Test
- Filter-/Paging-Test
- Einzel-Event-Test
- Sources-Test
- PHPUnit-Tests
- Java-Client manuell und später automatisiert prüfen

### Lehrplan-Folge

curl-Tests vor PHPUnit ausbauen, damit HTTP-Vertrag sichtbar wird. Danach Unit-Tests und Integrationstests ergänzen.

## Gap 8: Deployment und Betrieb

### Ist

PHP Development Server.

### Soll

- Start lokal
- Deployment ohne `.htaccess`
- DocumentRoot-Varianten
- IONOS Shared Hosting oder VPS einordnen
- `.env` sicher ablegen
- `var/log` und `var/cache/rate-limit` beschreibbar machen
- DB-User und Rechte prüfen
- Healthcheck und Minimaltests durchführen

### Lehrplan-Folge

Deployment nicht erst am Ende erwähnen. Schon früh erklären, welche Pfade und Konfigurationen später relevant sind.

## Konsequenz

Die nächsten Schritte müssen parallel geführt werden:

```text
API-Vertrag -> Datenbank -> PHP-Service -> curl-Test -> Java-Client -> Dokumentation -> Commit
```

So bleibt jede Änderung fachlich geschlossen.

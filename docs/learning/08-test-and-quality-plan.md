# Test and Quality Plan

## Ziel

Tests sollen nicht nur beweisen, dass etwas funktioniert. Sie sollen zeigen, an welcher Stelle etwas scheitert:

- Netzwerk?
- Routing?
- JSON?
- Authentifizierung?
- Autorisierung?
- Validierung?
- Datenbank?
- Client-Verarbeitung?

## Teststufen

| Stufe | Werkzeug | Zweck |
|---|---|---|
| Smoke-Test | curl | Ist der Service erreichbar? |
| Contract-Test | curl | Hält der Service den HTTP-Vertrag ein? |
| Unit-Test PHP | PHPUnit | Einzelne Klassen prüfen |
| Integrationstest PHP | PHPUnit + Test-DB | Service-Logik mit DB prüfen |
| Client-Test manuell | Java UI | Bedienbarkeit prüfen |
| Client-Test später | ggf. JUnit | Clientlogik automatisiert prüfen |

## Aktuelle V1-Tests

### Schreiben

```bash
curl -i -X POST "http://127.0.0.1:8080/api/logs" \
  -H "Content-Type: application/json; charset=utf-8" \
  --data-binary '{"level":"INFO","service":"smoke-test","message":"hello"}'
```

Erwartung:

- HTTP 201
- JSON-Antwort
- ID vorhanden

### Lesen

```bash
curl -i "http://127.0.0.1:8080/api/logs?limit=5"
```

Erwartung:

- HTTP 200
- Items vorhanden

## Ziel-API-Tests

### Health

```bash
curl -i "http://127.0.0.1:8080/index.php?route=/api/v1/health"
```

Erwartung:

- HTTP 200
- `ok=true`
- `requestId`
- Serviceinformationen

### Ingest ohne Token

```bash
curl -i -X POST "http://127.0.0.1:8080/index.php?route=/api/v1/ingest/events" \
  -H "Content-Type: application/json" \
  --data-binary '{"message":"test"}'
```

Erwartung später:

- HTTP 401
- `authentication_required`

### Ingest mit Source-Token

```bash
curl -i -X POST "http://127.0.0.1:8080/index.php?route=/api/v1/ingest/events" \
  -H "Authorization: Bearer DEV_SOURCE_TOKEN" \
  -H "Content-Type: application/json" \
  --data-binary '{
    "occurredAt": "2026-04-25T10:00:00Z",
    "observedAt": "2026-04-25T10:00:01Z",
    "severityNumber": 17,
    "severityText": "ERROR",
    "eventName": "learning.test",
    "message": "Test event",
    "serviceName": "learning",
    "traceId": "trace-123",
    "correlationId": "corr-123",
    "attributes": {
      "lesson": "ingest"
    }
  }'
```

Erwartung später:

- HTTP 202
- `ingestRequestId`
- `eventIds`
- `accepted`

### Lesen mit Client-Token

```bash
curl -i \
  -H "Authorization: Bearer DEV_CLIENT_TOKEN" \
  "http://127.0.0.1:8080/index.php?route=/api/v1/events&page=1&pageSize=20"
```

Erwartung:

- HTTP 200
- `data.items`
- `data.total`
- `data.page`
- `data.pageSize`

## Negativtests

| Test | Erwartung |
|---|---|
| Body kein JSON | 400 `invalid_json` |
| Root ist Array | 400 `invalid_json_document` |
| falscher Content-Type | 415 `unsupported_media_type` |
| Payload zu groß | 413 `payload_too_large` |
| severityNumber außerhalb 1..24 | 422 `invalid_severity_number` |
| Pflichtfeld fehlt | 422 `invalid_field` |
| kein Token | 401 `authentication_required` |
| falscher Token | 401 `invalid_token` |
| falscher Principal | 403 `invalid_principal` oder `forbidden` |
| Scope fehlt | 403 `forbidden` |
| Source-IP nicht erlaubt | 403 `source_network_denied` |
| zu viele Requests | 429 `rate_limit_exceeded` |

## PHPUnit-Plan

### Erste Tests

- `ConfigTest`
- `JsonResponseTest`
- `RequestTest`
- `IngestEventValidatorTest`

### Spätere Tests

- `TokenHasherTest`
- `AuthorizationServiceTest`
- `FileRateLimiterTest`
- `LogRepositoryTest`
- `IngestServiceIntegrationTest`

## Java-Client-Prüfung

Manuell:

1. Service starten.
2. Client starten.
3. Healthcheck klicken.
4. Eventliste laden.
5. Filter setzen.
6. Sortierung klicken.
7. Detail öffnen.
8. Related Events laden.
9. Test-Log senden.
10. CSV exportieren.
11. JSON exportieren.
12. Fehler testen: falsches Token, Service aus.

## Qualitätsregeln

- Keine Tokens in Logs.
- Keine Stacktraces an Clients.
- `requestId` in Fehlern sichtbar.
- `service.log` nur technische interne Hinweise.
- Audit enthält sicherheitsrelevante Zugriffe.
- Datenbankfehler werden nicht roh ausgegeben.
- Tests sind reproduzierbar.

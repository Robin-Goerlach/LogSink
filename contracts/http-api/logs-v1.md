# SASD Log API V0/V1

Diese Datei beschreibt die aktuelle HTTP-Schnittstelle zwischen LogSink-Service und Clients.

Wichtig: Der Name dieser Datei lautet noch `logs-v1.md`. Inhaltlich dokumentiert sie aber den aktuell real betriebenen V0/V1-Zwischenstand. Der Service ist bewusst einfach, noch ungeschützt und hat noch kein echtes Routing.

## Status

Aktueller Stand:

```text
V0/V1 raw log sink
```

Eigenschaften:

```text
ungeschützt
kein Bearer-Token
kein Principal
keine Scopes
kein echtes Routing
Request-Body wird unverändert gespeichert
GET liest die letzten gespeicherten Meldungen
```

## Aktueller IONOS-Endpunkt

Der funktionierende Shared-Hosting-Endpunkt lautet:

```text
http://api.sasd.de/logsink/index.php
```

Schreiben:

```http
POST /logsink/index.php
Content-Type: beliebig
```

Lesen:

```http
GET /logsink/index.php?limit=100
```

## Lokale Entwicklung

Je nach Start des PHP-Development-Servers kann lokal weiterhin eine URL wie diese genutzt werden:

```text
http://127.0.0.1:8080/api/logs
```

Wichtig: Der aktuelle PHP-Service wertet den Pfad noch nicht als echte Route aus. Entscheidend sind derzeit vor allem:

```text
HTTP-Methode GET oder POST
Query-Parameter limit bei GET
Request-Body bei POST
```

## Logmeldung schreiben

### Request

```http
POST /logsink/index.php
Content-Type: application/json; charset=utf-8
User-Agent: SASD-curl-json-sender/0.1
```

Der komplette HTTP-Request-Body wird unverändert gespeichert. Der Body darf im aktuellen V0/V1-Stand JSON, Text oder ein anderes Format sein.

### Beispiel IONOS

```bash
curl -i -X POST "http://api.sasd.de/logsink/index.php" \
  -H "Content-Type: application/json; charset=utf-8" \
  -H "User-Agent: SASD-contract-example/0.1" \
  --data-binary '{"level":"INFO","service":"demo","message":"Hallo LogSink"}'
```

### Erfolgsantwort

```json
{
  "status": "created",
  "id": 123,
  "receivedAt": "2026-04-28 13:52:33.685429",
  "rawMessageSize": 67,
  "payloadSha256": "..."
}
```

## Logmeldungen lesen

### Request

```http
GET /logsink/index.php?limit=100
```

`limit` ist optional. Der aktuelle PHP-Service begrenzt intern auf maximal 1000 Einträge.

### Beispiel IONOS

```bash
curl -i "http://api.sasd.de/logsink/index.php?limit=10"
```

### Erfolgsantwort

```json
{
  "status": "ok",
  "items": [
    {
      "id": 123,
      "received_at": "2026-04-28 13:52:33.685429",
      "source_ip": "87.162.149.185",
      "source_port": 33930,
      "http_method": "POST",
      "request_uri": "/logsink/index.php",
      "content_type": "application/json; charset=utf-8",
      "user_agent": "SASD-contract-example/0.1",
      "raw_message_size": 67,
      "payload_sha256": "...",
      "raw_message_text": "{...}",
      "raw_message_base64": "..."
    }
  ]
}
```

## Fehlerantworten

Aktuell sind Fehlerantworten bewusst einfach gehalten.

```json
{
  "status": "error",
  "error": "internal_server_error",
  "message": "Internal server error."
}
```

Im Entwicklungsmodus kann `message` technische Details enthalten. Im produktionsnahen Betrieb darf das später nicht passieren.

## Aktuelle Beispiel-Clients

Der aktuelle V0/V1-Vertrag wird durch folgende Beispiele genutzt:

```text
examples/log-senders/curl
examples/log-readers/curl
examples/log-senders/php
examples/log-readers/php
examples/log-senders/java
examples/csharp
clients/java-log-viewer
```

## Geplante Ziel-API

Später soll der Vertrag in Richtung einer saubereren API weiterentwickelt werden.

Mögliche Zielrouten:

```http
GET  /index.php?route=/api/v1/health
POST /index.php?route=/api/v1/ingest/events
GET  /index.php?route=/api/v1/events?limit=100
```

Geplante Eigenschaften:

```text
Request-ID
einheitliches JSON-Antwortmodell
strukturierte Events
Bearer-Token
Source-Principal
Scopes
Audit-Logging
defensive Fehlerausgabe
```

## Stabilitätsnotiz

Dieser Vertrag ist ein Arbeitsvertrag für den aktuellen Lernstand. Clients sollten sich im Moment nur auf die dokumentierten V0/V1-Felder verlassen.

Breaking Changes sind im Rahmen des Lehrprojekts möglich und werden dokumentiert, sobald der Service von der Raw-Body-V0/V1 in eine strukturierte API überführt wird.

# SASD Log API V1

Diese Datei beschreibt die aktuelle HTTP-Schnittstelle zwischen LogSink-Service und Clients.

## Allgemeines

Basis-URL lokal:

```text
http://127.0.0.1:8080
```

Die V1 ist bewusst ungeschützt. Es gibt keine Authentifizierung und keine Autorisierung.

## Logmeldung schreiben

```http
POST /api/logs
Content-Type: beliebig
```

Der komplette HTTP-Request-Body wird unverändert gespeichert.

### Beispiel

```bash
curl -i -X POST "http://127.0.0.1:8080/api/logs" \
  -H "Content-Type: application/json; charset=utf-8" \
  --data-binary '{"level":"INFO","service":"demo","message":"Hallo LogSink"}'
```

### Erfolgsantwort

```json
{
  "status": "created",
  "id": 123,
  "receivedAt": "2026-04-24 10:15:01.123456",
  "rawMessageSize": 67,
  "payloadSha256": "..."
}
```

## Logmeldungen lesen

```http
GET /api/logs?limit=100
```

`limit` ist optional. Der aktuelle PHP-Service begrenzt intern auf maximal 1000 Einträge.

### Beispiel

```bash
curl -i "http://127.0.0.1:8080/api/logs?limit=10"
```

### Erfolgsantwort

```json
{
  "status": "ok",
  "items": [
    {
      "id": 123,
      "received_at": "2026-04-24 10:15:01.123456",
      "source_ip": "127.0.0.1",
      "source_port": 54321,
      "http_method": "POST",
      "request_uri": "/api/logs",
      "content_type": "application/json; charset=utf-8",
      "user_agent": "curl/8.0.0",
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

## Stabilitätsnotiz

Diese Schnittstelle ist der V1-Vertrag. Clients sollten sich nur auf die hier dokumentierten Felder verlassen. Zusätzliche Felder können später ergänzt werden.

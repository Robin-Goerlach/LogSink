# Schreibende Logging-Clients - Plan und Tests

## Warum dieses Dokument?

Bisher haben wir vor allem den Java-Viewer betrachtet. Der Java-Viewer liest Logmeldungen aus dem Service.

Ein Logging-Service braucht aber auch schreibende Clients, also Beispielprogramme, die Logmeldungen an den Service senden.

Diese Clients sind aus zwei Gründen wichtig:

1. Sie zeigen, wie andere Programme den Service benutzen.
2. Sie sind Testwerkzeuge für den Service.

## Ziel

Wir erstellen im Projekt schrittweise Beispiel-Clients für das Schreiben von Logmeldungen.

Mögliche Struktur:

```text
examples/
├── curl/
├── php/
├── java/
├── javascript/
└── dotnet/
```

Alternativ:

```text
clients/
├── java-log-viewer/
├── java-log-sender/
├── php-log-sender/
└── dotnet-log-sender/
```

Für ein Lehrprojekt ist `examples/` wahrscheinlich besser, weil diese Programme vor allem Beispiele und Testwerkzeuge sind.

## Entwicklungsstufen

Die Sender-Clients wachsen mit dem Service.

### V0: ungeschütztes POST

Der Client sendet einfach einen Body an den aktuellen V1-Service.

Beispiel mit curl:

```bash
curl -i -X POST "http://api.sasd.de/logsink/index.php" \
  -H "Content-Type: application/json; charset=utf-8" \
  --data-binary '{"level":"INFO","service":"curl-example","message":"Hello from curl"}'
```

### V1: strukturierter JSON-Body

Sobald der Service strukturierte Events erwartet, senden Clients ein definiertes Eventformat.

### V2: Bearer-Token

Clients senden:

```http
Authorization: Bearer <token>
```

### V3: Source-Principal

Schreibende Clients verwenden einen Source-Token, keinen Client-/Read-Token.

### V4: Scope `events.ingest`

Der Token benötigt den Scope:

```text
events.ingest
```

### V5: robuste Fehlerbehandlung

Clients behandeln:

- 400 ungültiger Request,
- 401 kein oder falscher Token,
- 403 keine Berechtigung,
- 413 Payload zu groß,
- 415 falscher Content-Type,
- 422 fachlich ungültiger Body,
- 429 Rate-Limit,
- 500 Serverfehler.

## Geplante Beispiele

### curl-Beispiele

Ordner:

```text
examples/curl/
```

Dateien:

```text
post-raw-log.sh
post-json-event.sh
post-json-event-with-token.sh
```

Vorteil: sofort verständlich und gut für Smoke-Tests.

### PHP-Sender

Ordner:

```text
examples/php-log-sender/
```

Lerninhalt:

- `file_get_contents()` mit stream context oder cURL-Erweiterung,
- JSON erzeugen,
- HTTP-Header setzen,
- Fehler prüfen.

### Java-Sender

Ordner:

```text
examples/java-log-sender/
```

Lerninhalt:

- Java `HttpClient`,
- JSON mit Jackson,
- Timeout,
- Statuscode-Auswertung.

### JavaScript/Node-Sender

Später möglich:

```text
examples/node-log-sender/
```

Lerninhalt:

- `fetch`,
- JSON,
- Header,
- async/await.

### C#/.NET-Sender

Später möglich:

```text
examples/dotnet-log-sender/
```

Lerninhalt:

- `HttpClient`,
- JSON-Serialisierung,
- WPF-/Service-Integration.

## Teststrategie

### Positivtest

1. Sender sendet Testmeldung.
2. Service antwortet erfolgreich.
3. curl GET liest die letzten Logs.
4. Java-Viewer zeigt die neue Meldung.

### Negativtests

- falsche URL,
- falscher Content-Type,
- leerer Body,
- ungültiges JSON,
- später: fehlender Token,
- später: falscher Token,
- später: Token ohne Scope.

## Dokumentationsregel

Jeder Sender bekommt eine eigene README:

```text
examples/php-log-sender/README.md
examples/java-log-sender/README.md
```

Jede README erklärt:

- Zweck,
- Voraussetzungen,
- Konfiguration,
- Start,
- erwartetes Ergebnis,
- typische Fehler.

## LS-Schritte

### LS-021: Schreibende Beispiel-Clients planen

Dieses Dokument ist der Startpunkt.

### LS-022: curl-Logging-Beispiele erstellen

Erste Skripte für ungeschütztes POST und spätere Token-Variante.

### LS-023: PHP-Logging-Client erstellen

Einfacher PHP-Sender.

### LS-024: Java-Logging-Client erstellen

Einfacher Java-Sender.

### LS-025: Sender-Clients in Testplan aufnehmen

Service-Test besteht aus:

```text
Sender -> Service -> Datenbank -> Viewer
```

### LS-026: Sender-Clients an Authentifizierung anpassen

Sobald Bearer-Tokens eingeführt werden, werden die Sender angepasst.

## Wichtig

Die Sender-Clients sind nicht nur Zusatzkomfort. Sie sind notwendig, damit wir später prüfen können, ob die Schreibseite des Logging-Services wirklich funktioniert.

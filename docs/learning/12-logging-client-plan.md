# Schreibende Logging-Clients - Plan und Tests

## Warum dieses Dokument?

Bisher haben wir vor allem den Java-Viewer betrachtet. Der Java-Viewer liest Logmeldungen aus dem Service.

Ein Logging-Service braucht aber auch schreibende Clients, also Beispielprogramme, die Logmeldungen an den Service senden.

Diese Clients sind aus zwei Gründen wichtig:

1. Sie zeigen, wie andere Programme den Service benutzen.
2. Sie sind Testwerkzeuge für den Service.

## Nummerierung

Die LS-Nummern `LS-021` bis `LS-024` sind im Haupt-Lehrplan bereits für API-Umbauten belegt:

```text
LS-021: Request-ID einführen
LS-022: Einheitliches JSON-Antwortmodell einführen
LS-023: Einfaches Routing einführen
LS-024: Front-Controller-Route-Parameter ergänzen
```

Deshalb verwenden die Beispiel-Clients eine eigene Nummerierung:

```text
EX-001, EX-002, EX-003, ...
```

## Zielstruktur

Für das Lehrprojekt verwenden wir `examples/`, weil diese Programme vor allem Beispiele und Testwerkzeuge sind.

```text
examples/
├── README.md
├── log-senders/
│   ├── README.md
│   └── curl/
└── log-readers/
    ├── README.md
    └── curl/
```

## Warum auch `examples/log-readers/`?

Der Java-Viewer ist der eigentliche grafische Reader.

Trotzdem sind kleine curl-Reader nützlich, weil man damit schnell prüfen kann:

- Ist der Service erreichbar?
- Kommt JSON zurück?
- Ist eine frisch gesendete Meldung sichtbar?
- Funktioniert ein Roundtrip ohne GUI?

Die Reader-Beispiele sind also vor allem Diagnose- und Testwerkzeuge.

## Entwicklungsstufen

Die Sender-Clients wachsen mit dem Service.

### V0: ungeschütztes POST

Der Client sendet einfach einen Body an den aktuellen V1-Service.

Aktueller IONOS-Testbetrieb:

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

## Beispiel-Schritte

### EX-001: Struktur für Beispiele

Ziel:

```text
examples/log-senders
examples/log-readers
```

### EX-002: curl-Logging-Beispiele erstellen

Erste Skripte für ungeschütztes POST.

### EX-003: curl-Reader zur Verifikation erstellen

GET-Beispiel, um die letzten Meldungen zu lesen.

### EX-004: Roundtrip-Smoke-Test erstellen

Eine Meldung senden und danach prüfen, ob sie über GET sichtbar ist.

### EX-005: PHP-Logging-Client erstellen

Einfacher PHP-Sender.

### EX-006: Java-Logging-Client erstellen

Einfacher Java-Sender.

### EX-007: Sender-Clients an Authentifizierung anpassen

Sobald Bearer-Tokens eingeführt werden, werden die Sender angepasst.

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

## Wichtig

Die Sender-Clients sind nicht nur Zusatzkomfort. Sie sind notwendig, damit wir später prüfen können, ob die Schreibseite des Logging-Services wirklich funktioniert.

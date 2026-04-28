# Schreibende Logging-Clients - Plan und Tests

## Warum dieses Dokument?

Bisher haben wir vor allem den Java-Viewer betrachtet. Der Java-Viewer liest Logmeldungen aus dem Service.

Ein Logging-Service braucht aber auch schreibende Clients, also Beispielprogramme, die Logmeldungen an den Service senden.

Diese Clients sind aus zwei Gründen wichtig:

1. Sie zeigen, wie andere Programme den Service benutzen.
2. Sie sind Testwerkzeuge für den Service.

## Nummerierung

Die LS-Nummern `LS-021` bis `LS-024` sind im Haupt-Lehrplan bereits für API-Umbauten belegt. Deshalb verwenden die Beispiel-Clients eine eigene Nummerierung:

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
│   ├── curl/
│   └── php/
└── log-readers/
    ├── README.md
    └── curl/
```

## Entwicklungsstufen

Die Sender-Clients wachsen mit dem Service:

```text
V0 ungeschütztes POST
V1 strukturierter JSON-Body
V2 Bearer-Token
V3 Source-Principal
V4 Scope events.ingest
V5 robuste Fehlerbehandlung
```

## Beispiel-Schritte

### EX-001: Struktur für Beispiele

Status: erledigt.

### EX-002: curl-Logging-Beispiele erstellen

Status: erledigt.

### EX-003: curl-Reader zur Verifikation erstellen

Status: erledigt.

### EX-004: Roundtrip-Smoke-Test erstellen

Status: erledigt.

### EX-005: PHP-Logging-Client erstellen

Status: erledigt mit V0-Beispielen.

Enthalten:

```text
examples/log-senders/php/post-json-log.php
examples/log-senders/php/post-text-log.php
examples/log-senders/php/post-error-log.php
examples/log-senders/php/roundtrip-smoke-test.php
examples/log-senders/php/src/LogSinkClient.php
```

### EX-006: Java-Logging-Client erstellen

Status: offen.

### EX-007: Sender-Clients an Authentifizierung anpassen

Status: offen.

## Teststrategie

### Positivtest

1. Sender sendet Testmeldung.
2. Service antwortet erfolgreich.
3. Reader liest die letzten Logs.
4. Java-Viewer zeigt die neue Meldung.

### Negativtests

- falsche URL,
- falscher Content-Type,
- leerer Body,
- ungültiges JSON,
- später: fehlender Token,
- später: falscher Token,
- später: Token ohne Scope.

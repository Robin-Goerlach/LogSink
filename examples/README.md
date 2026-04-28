# Examples

Dieser Ordner enthält Beispielcode und kleine Testwerkzeuge für LogSink.

## Zweck

Die Beispiele sind nicht der produktive Service. Sie zeigen, wie andere Programme den LogSink-Service benutzen können.

Aktuell gibt es zwei Arten von Beispielen:

```text
examples/log-senders   Programme/Skripte, die Logmeldungen an LogSink senden
examples/log-readers   Programme/Skripte, die Logmeldungen zur Kontrolle lesen
```

## Warum nicht alles unter `clients/`?

`clients/` ist für eigenständige Anwendungen gedacht, z. B. den Java-Viewer.

`examples/` ist für kleine, didaktische Beispielprogramme gedacht. Sie dienen auch als Smoke-Tests.

## Aktueller Stand

Die Beispiele zielen auf den aktuellen ungeschützten V0/V1-Service:

```text
POST http://api.sasd.de/logsink/index.php
GET  http://api.sasd.de/logsink/index.php?limit=5
```

Lokal kann je nach Startart auch funktionieren:

```text
POST http://127.0.0.1:8080/api/logs
GET  http://127.0.0.1:8080/api/logs?limit=5
```

## Wichtiger Sicherheitshinweis

Die aktuelle V0/V1 ist bewusst ungeschützt. Später müssen die Beispiele erweitert werden um:

- strukturierte Events,
- Bearer-Token,
- Source-Principal,
- Scope `events.ingest`,
- bessere Fehlerbehandlung.

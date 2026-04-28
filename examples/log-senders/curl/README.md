# curl Log Sender Examples

Dieser Ordner enthält kleine curl-Skripte, die Logmeldungen an LogSink senden.

## Konfiguration

Die Skripte verwenden die Umgebungsvariable `LOGSINK_URL`.

Aktueller IONOS-Testbetrieb:

```bash
export LOGSINK_URL="http://api.sasd.de/logsink/index.php"
```

Lokale Entwicklung mit PHP Development Server:

```bash
export LOGSINK_URL="http://127.0.0.1:8080/api/logs"
```

Wenn `LOGSINK_URL` nicht gesetzt ist, verwenden die Skripte als Default:

```text
http://api.sasd.de/logsink/index.php
```

## Skripte

```text
post-json-log.sh          sendet eine einfache JSON-Logmeldung
post-text-log.sh          sendet eine einfache Text-Logmeldung
post-error-log.sh         sendet eine JSON-Fehlermeldung
roundtrip-smoke-test.sh   sendet eine Meldung und liest danach die letzten Meldungen
```

## Ausführen

```bash
chmod +x examples/log-senders/curl/*.sh

./examples/log-senders/curl/post-json-log.sh
./examples/log-senders/curl/post-text-log.sh
./examples/log-senders/curl/post-error-log.sh
./examples/log-senders/curl/roundtrip-smoke-test.sh
```

## Warum mehrere Varianten?

Die aktuelle V0/V1 speichert den Body unverändert. Deshalb ist es sinnvoll, unterschiedliche Body-Arten zu testen:

- JSON,
- plain text,
- Fehlerereignis.

Später wird der Service strukturierte Events erwarten. Dann werden diese Beispiele angepasst.

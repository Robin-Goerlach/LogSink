# PHP Log Reader Example

Dieser Ordner enthält ein kleines PHP-Beispiel zum Lesen von Logmeldungen aus LogSink.

## Zweck

Der Java-Viewer ist der grafische Reader. Dieses PHP-Beispiel ist ein technisches Prüfwerkzeug.

Es zeigt:

- wie PHP Logmeldungen per HTTP GET liest,
- wie das aktuelle V0/V1-JSON ausgegeben wird,
- wie man ohne Browser oder Java-Viewer schnell prüfen kann, ob der Service Daten liefert.

## Voraussetzung

Der PHP-Sender-Client muss vorhanden sein, weil dieses Reader-Beispiel dieselbe kleine Klasse nutzt:

```text
examples/log-senders/php/src/LogSinkClient.php
```

Das ist für V0 bewusst einfach. Später kann diese Klasse in einen gemeinsamen Ordner verschoben werden, z. B.:

```text
examples/php-common/src/LogSinkClient.php
```

## Konfiguration

Aktueller IONOS-Testbetrieb:

```bash
export LOGSINK_URL="http://api.sasd.de/logsink/index.php"
```

Lokale Entwicklung:

```bash
export LOGSINK_URL="http://127.0.0.1:8080/api/logs"
```

## Nutzung

```bash
php examples/log-readers/php/get-latest-logs.php
php examples/log-readers/php/get-latest-logs.php 10
```

Oder direkt ausführbar:

```bash
chmod +x examples/log-readers/php/get-latest-logs.php
./examples/log-readers/php/get-latest-logs.php 10
```

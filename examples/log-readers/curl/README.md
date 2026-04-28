# curl Log Reader Examples

Dieser Ordner enthält einfache curl-Beispiele zum Lesen von Logmeldungen.

## Konfiguration

```bash
export LOGSINK_URL="http://api.sasd.de/logsink/index.php"
```

Lokal:

```bash
export LOGSINK_URL="http://127.0.0.1:8080/api/logs"
```

## Abruf

```bash
chmod +x examples/log-readers/curl/*.sh

./examples/log-readers/curl/get-latest-logs.sh
./examples/log-readers/curl/get-latest-logs.sh 10
```

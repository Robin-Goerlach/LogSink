# SASD Log Sink Service

Dieser Ordner enthält den PHP-8.4-Service von SASD LogSink.

## Aufgabe

Der Service nimmt Logmeldungen per HTTP entgegen und speichert den kompletten Request-Body unverändert in MariaDB.

## Start

```bash
cp .env.example .env
php -S 127.0.0.1:8080 public/index.php
```

## Schreiben

```bash
curl -i -X POST "http://127.0.0.1:8080/api/logs" \
  -H "Content-Type: application/json; charset=utf-8" \
  --data-binary '{"level":"INFO","service":"demo","message":"Hallo LogSink"}'
```

## Lesen

```bash
curl -i "http://127.0.0.1:8080/api/logs?limit=10"
```

## Struktur

```text
services/log-sink/
├── .env.example
├── index.php
├── public/
│   └── index.php
├── src/
│   ├── App.php
│   ├── Bootstrap.php
│   ├── Config.php
│   ├── Database.php
│   ├── LogRepository.php
│   └── ServiceLogger.php
└── var/
    └── log/
        └── .gitkeep
```

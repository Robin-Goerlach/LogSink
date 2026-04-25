# Architektur

## Überblick

SASD LogSink besteht aktuell aus drei Teilen:

1. einem PHP-Service, der HTTP-Requests annimmt,
2. einer MariaDB-Datenbank, die Rohmeldungen und Metadaten speichert,
3. einem Java-Viewer, der Logs per HTTP liest und anzeigt.

```text
Client / Anwendung
        |
        | POST /api/logs
        v
PHP Log Sink Service
        |
        | INSERT INTO log_entries
        v
MariaDB 10.11
        ^
        | SELECT FROM v_log_entries
        |
Java Log Viewer
        |
        | GET /api/logs?limit=100
        v
PHP Log Sink Service
```

## PHP Log Sink Service

Der Service ist bewusst einfach gehalten:

- kein Framework,
- keine `.htaccess`,
- Frontcontroller im Service-Root und unter `public/`,
- eigene kleine Klassen für Konfiguration, Datenbank, Repository und Logging.

Die wichtigste technische Entscheidung ist, dass der Request-Body unverändert gespeichert wird. Deshalb verwendet die Datenbank für die eigentliche Logmeldung ein `LONGBLOB`.

## MariaDB

Die Tabelle `log_entries` speichert:

- die Rohmeldung als `raw_message`,
- technische Request-Metadaten,
- Größe der Rohmeldung,
- SHA-256-Hash der Rohmeldung.

Größe und Hash werden in der Datenbank per Trigger berechnet.

## Java Log Viewer

Der Java-Viewer nutzt das HTTP-GET-Interface des PHP-Service. Er greift nicht direkt auf die Datenbank zu.

Dadurch bleibt die Kopplung gering:

- der Viewer kennt nur die HTTP API,
- die Datenbankstruktur kann später verändert werden,
- ein anderer Service könnte dieselbe API bereitstellen.

## Bewusste Nicht-Ziele der V1

Die V1 enthält absichtlich nicht:

- Authentifizierung,
- Autorisierung,
- Mandantenfähigkeit,
- Rate Limiting,
- komplexe Suche,
- Logrotation oder Archivierung,
- Produktionshärtung.

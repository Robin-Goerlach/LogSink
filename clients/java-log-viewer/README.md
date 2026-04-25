# SASD Log Viewer Java

**SASD Log Viewer Java** ist ein kleiner Java-Client für den PHP-Service **SASD LogSink**.

Der Client ruft Logmeldungen per `GET` vom PHP-Service ab und zeigt sie in einer Swing-Tabelle an. Die Tabelle kann nach Spalten sortiert werden.

## Funktionen

- Abruf von Logmeldungen über HTTP `GET`
- Anzeige in einer Java-Swing-Oberfläche
- Sortieren nach Spalten
- einstellbare Service-URL
- einstellbares Limit
- Detailanzeige per Doppelklick
- asynchroner Abruf, damit die Oberfläche nicht blockiert
- Maven-Projekt
- Java 17 kompatibel

## Erwartetes PHP-Service-Interface

Der Client erwartet das vom PHP-Service gelieferte JSON-Format:

```http
GET /api/logs?limit=100
```

Beispielantwort:

```json
{
  "status": "ok",
  "items": [
    {
      "id": 1,
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

## Projektstruktur

```text
sasd-log-viewer-java/
├── pom.xml
├── README.md
├── LICENSE
├── .gitignore
└── src/
    └── main/
        └── java/
            └── de/
                └── sasd/
                    └── logsink/
                        └── viewer/
                            ├── Main.java
                            ├── client/
                            │   └── LogServiceClient.java
                            ├── model/
                            │   ├── LogEntry.java
                            │   └── LogResponse.java
                            └── ui/
                                ├── LogDetailDialog.java
                                ├── LogTableModel.java
                                └── LogViewerFrame.java
```

## Bauen

```bash
mvn clean package
```

Danach liegt die ausführbare JAR-Datei hier:

```text
target/sasd-log-viewer-java-1.0.0.jar
```

## Starten

```bash
java -jar target/sasd-log-viewer-java-1.0.0.jar
```

## Standard-URL

Beim Start ist voreingestellt:

```text
http://127.0.0.1:8080/api/logs
```

Diese URL kann in der Oberfläche geändert werden.

## Bedienung

1. PHP-Service starten.
2. Java-Client starten.
3. Optional URL und Limit ändern.
4. Auf **Aktualisieren** klicken.
5. Spaltenüberschriften anklicken, um zu sortieren.
6. Logzeile doppelklicken, um Details anzuzeigen.

## Lizenz

MIT License. Siehe `LICENSE`.

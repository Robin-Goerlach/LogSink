# SASD Log Viewer Java

**SASD Log Viewer Java** ist ein kleiner Java-Client für den PHP-Service **SASD LogSink**.

Der Client ruft Logmeldungen per HTTP `GET` vom PHP-Service ab und zeigt sie in einer Swing-Tabelle an. Die Tabelle kann nach Spalten sortiert und gefiltert werden.

## Funktionen

- Abruf von Logmeldungen über HTTP `GET`
- Anzeige in einer Java-Swing-Oberfläche
- Sortieren nach Spalten
- Filterfeld für schnelle Suche
- einstellbare Service-URL
- einstellbares Limit
- Detailanzeige per Doppelklick
- asynchroner Abruf, damit die Oberfläche nicht blockiert
- Konfiguration über `client-settings.json`
- Maven-Projekt
- Java 17 kompatibel

## Erwartetes PHP-Service-Interface

Der aktuelle V1-Client erwartet das vom PHP-Service gelieferte JSON-Format:

```http
GET /api/logs?limit=100
```

Bei IONOS ohne Rewrite wird aktuell stattdessen die echte PHP-Datei aufgerufen:

```http
GET /logsink/index.php?limit=100
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
java-log-viewer/
├── .gitignore
├── LICENSE
├── pom.xml
├── README.md
├── client-settings.example.json
├── client-settings.json              # lokal, wird nicht committed
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
                            ├── config/
                            │   ├── ClientSettings.java
                            │   └── ClientSettingsLoader.java
                            ├── model/
                            │   ├── LogEntry.java
                            │   └── LogResponse.java
                            └── ui/
                                ├── LogDetailDialog.java
                                ├── LogTableModel.java
                                ├── LogViewerFrame.java
                                └── SimpleDocumentListener.java
```

## Konfiguration

Die Vorlage liegt hier:

```text
client-settings.example.json
```

Für lokale Nutzung kopieren:

```bash
cp client-settings.example.json client-settings.json
```

Beispiel für lokale Entwicklung:

```json
{
  "serviceUrl": "http://127.0.0.1:8080/api/logs",
  "defaultLimit": 100,
  "timeoutSeconds": 15
}
```

Beispiel für den aktuellen IONOS-Testbetrieb:

```json
{
  "serviceUrl": "http://api.sasd.de/logsink/index.php",
  "defaultLimit": 100,
  "timeoutSeconds": 15
}
```

`client-settings.json` wird bewusst ignoriert, weil dort später auch lokale URLs oder Tokens stehen können.

## Suchreihenfolge der Konfiguration

Der Client sucht die Konfiguration in dieser Reihenfolge:

1. Java-Systemproperty:

   ```bash
   -Dlogsink.viewer.config=/pfad/zur/client-settings.json
   ```

2. Umgebungsvariable:

   ```bash
   LOGSINK_VIEWER_CONFIG=/pfad/zur/client-settings.json
   ```

3. Datei im aktuellen Arbeitsverzeichnis:

   ```text
   client-settings.json
   ```

4. Datei bei Start aus dem Repository-Root:

   ```text
   clients/java-log-viewer/client-settings.json
   ```

5. Benutzerbezogene Datei:

   ```text
   ~/.logsink/java-viewer-settings.json
   ```

Wenn keine Datei gefunden wird, verwendet der Client eingebaute Defaults.

## Bauen

Aus dem Client-Verzeichnis:

```bash
mvn clean package
```

Aus dem Repository-Root:

```bash
mvn -f clients/java-log-viewer/pom.xml clean package
```

Danach liegt die ausführbare JAR-Datei hier:

```text
target/sasd-log-viewer-java-1.0.0.jar
```

## Starten

Aus dem Client-Verzeichnis:

```bash
java -jar target/sasd-log-viewer-java-1.0.0.jar
```

Aus dem Repository-Root:

```bash
java -jar clients/java-log-viewer/target/sasd-log-viewer-java-1.0.0.jar
```

Mit expliziter Konfigurationsdatei:

```bash
java -Dlogsink.viewer.config=/pfad/zur/client-settings.json \
  -jar clients/java-log-viewer/target/sasd-log-viewer-java-1.0.0.jar
```

## Bedienung

1. PHP-Service starten oder Remote-Service erreichbar machen.
2. Java-Client starten.
3. Optional URL und Limit ändern.
4. Auf **Aktualisieren** klicken.
5. Spaltenüberschriften anklicken, um zu sortieren.
6. Filterfeld nutzen, um die Tabelle einzugrenzen.
7. Logzeile doppelklicken, um Details anzuzeigen.

## Lizenz

MIT License. Siehe `LICENSE`.

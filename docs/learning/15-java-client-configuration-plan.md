# Java Client Configuration Plan

## Problem

Aktuell ist die Service-URL im Java-Code hart codiert.

Lokale Entwicklung:

```java
private static final String DEFAULT_SERVICE_URL = "http://127.0.0.1:8080/api/logs";
```

IONOS-Test:

```java
private static final String DEFAULT_SERVICE_URL = "http://api.sasd.de/logsink/index.php";
```

Das ist für einen schnellen Test okay, aber keine saubere Lösung.

## Ziel

Der Java-Client lädt seine Konfiguration aus einer Datei.

Geplante Dateien:

```text
clients/java-log-viewer/client-settings.example.json
clients/java-log-viewer/client-settings.json
```

`client-settings.json` wird später ignoriert, weil dort lokale URLs und später Tokens stehen können.

## V1-Konfiguration

```json
{
  "serviceUrl": "http://api.sasd.de/logsink/index.php",
  "defaultLimit": 100,
  "timeoutSeconds": 15
}
```

## Spätere Ziel-Konfiguration

```json
{
  "baseUrl": "http://api.sasd.de/logsink/index.php",
  "routeParameter": "route",
  "apiVersionPath": "/api/v1",
  "defaultPageSize": 100,
  "timeoutSeconds": 15,
  "readToken": "",
  "ingestToken": "",
  "diagnosticLogFile": "${user.home}/.logsink/java-viewer.log"
}
```

## Umsetzungsplan

1. `ClientSettings.java` anlegen.
2. `ClientSettingsLoader.java` anlegen.
3. `Main.java` lädt Settings.
4. `LogViewerFrame` nutzt Settings statt Konstante.
5. README des Java-Clients aktualisieren.

## Tests

```bash
mvn -f clients/java-log-viewer/pom.xml clean package
java -jar clients/java-log-viewer/target/sasd-log-viewer-java-1.0.0.jar
```

Erwartung: Client startet, Service-URL ist aus Konfiguration vorbelegt, Aktualisieren lädt Logs.

## Commit-Vorschlag

```text
Add Java viewer settings file
```

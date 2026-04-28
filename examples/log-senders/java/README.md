# Java Log Sender Example

Dieser Ordner enthält einen einfachen Java-Client, der Logmeldungen an den aktuellen LogSink-Service sendet.

## Zweck

Der Java-Sender zeigt, wie eine Java-Anwendung Logmeldungen an LogSink absetzen kann.

Aktueller Flow:

```text
Java-Sender -> HTTP POST -> LogSink PHP-Service -> MariaDB
```

Der Roundtrip-Test prüft zusätzlich:

```text
Java-Sender -> Service -> Datenbank -> GET-API
```

## Voraussetzungen

- Java 17 oder neuer
- Maven
- Netzwerkzugriff auf den LogSink-Service

Prüfen:

```bash
java -version
mvn -version
```

## Konfiguration

Die Beispiele verwenden die Umgebungsvariable `LOGSINK_URL`.

Aktueller IONOS-Testbetrieb:

```bash
export LOGSINK_URL="http://api.sasd.de/logsink/index.php"
```

Lokale Entwicklung mit PHP Development Server:

```bash
export LOGSINK_URL="http://127.0.0.1:8080/api/logs"
```

Wenn `LOGSINK_URL` nicht gesetzt ist, wird als Default genutzt:

```text
http://api.sasd.de/logsink/index.php
```

## Bauen

Aus dem Repository-Root:

```bash
mvn -f examples/log-senders/java/pom.xml clean package
```

## Ausführen

```bash
java -cp examples/log-senders/java/target/classes de.sasd.logsink.examples.sender.PostJsonLog
java -cp examples/log-senders/java/target/classes de.sasd.logsink.examples.sender.PostTextLog
java -cp examples/log-senders/java/target/classes de.sasd.logsink.examples.sender.PostErrorLog
java -cp examples/log-senders/java/target/classes de.sasd.logsink.examples.sender.RoundtripSmokeTest
```

## Warum kein externer JSON-Parser?

Dieses Beispiel bleibt bewusst einfach und verwendet nur Java-Standardbibliothek:

- `java.net.http.HttpClient`
- `java.net.http.HttpRequest`
- `java.net.http.HttpResponse`
- `java.time.OffsetDateTime`

Die JSON-Strings werden für V0 bewusst einfach gebaut. Später, wenn strukturierte Events und Validierung wichtiger werden, kann ein echter JSON-Serializer ergänzt werden.

## Dateien

```text
HttpResult.java          einfache Response-Struktur
LogSinkHttpClient.java   kleine wiederverwendbare HTTP-Client-Klasse
PostJsonLog.java         sendet eine JSON-Logmeldung
PostTextLog.java         sendet eine Text-Logmeldung
PostErrorLog.java        sendet eine JSON-Fehlermeldung
RoundtripSmokeTest.java  sendet eine eindeutige Meldung und liest sie zurück
```

## Sicherheitshinweis

Die aktuelle V0/V1-API ist ungeschützt. Später muss dieser Client erweitert werden um:

- Bearer-Token,
- Source-Principal,
- Scope `events.ingest`,
- Timeouts,
- robuste Fehlerbehandlung,
- keine Ausgabe geheimer Tokens.

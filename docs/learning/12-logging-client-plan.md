# Schreibende Logging-Clients - Plan und Tests

## Warum dieses Dokument?

Bisher war der Java-Viewer der sichtbarste Client. Er liest Logmeldungen aus dem Service. Ein Logging-Service braucht aber auch schreibende Clients, also Programme oder Skripte, die Logmeldungen an den Service senden.

Diese Clients sind aus zwei Gründen wichtig:

1. Sie zeigen, wie andere Anwendungen den Service benutzen.
2. Sie dienen als Testwerkzeuge für den Service.

Der aktuelle V0/V1-Service speichert den HTTP-Request-Body bewusst unverändert. Das ist für den Lernstart einfach und gut sichtbar. Später wird daraus ein strukturierter Ingest-Endpunkt mit Authentifizierung, Autorisierung und Validierung.

## Nummerierung

Die LS-Nummern sind für den Hauptlehrplan reserviert. Damit es keine Konflikte gibt, verwenden Beispiel-Clients eine eigene Nummerierung:

```text
EX-001, EX-002, EX-003, ...
```

## Aktuelle Zielstruktur

```text
examples/
├── README.md
├── csharp/
├── log-readers/
│   ├── README.md
│   ├── curl/
│   └── php/
└── log-senders/
    ├── README.md
    ├── curl/
    ├── java/
    └── php/
```

## Aktueller Service-Endpunkt

Der aktuelle IONOS-Testbetrieb verwendet:

```text
http://api.sasd.de/logsink/index.php
```

Schreiben:

```http
POST /logsink/index.php
```

Lesen:

```http
GET /logsink/index.php?limit=10
```

Die geplante Ziel-API mit echten Routen kommt später. Deshalb müssen die Beispiele später angepasst werden.

## Entwicklungsstufen

```text
V0  ungeschütztes POST auf index.php
V1  strukturierter JSON-Body
V2  Bearer-Token
V3  Source-Principal
V4  Scope events.ingest
V5  robuste Fehlerbehandlung, Timeouts, Retry-Strategie
```

## Beispiel-Schritte

### EX-001: Struktur für Beispiele festlegen

Status: erledigt.

### EX-002: curl-Logging-Beispiele erstellen

Status: erledigt.

```text
examples/log-senders/curl/post-json-log.sh
examples/log-senders/curl/post-text-log.sh
examples/log-senders/curl/post-error-log.sh
```

### EX-003: curl-Reader zur Verifikation erstellen

Status: erledigt.

```text
examples/log-readers/curl/get-latest-logs.sh
```

### EX-004: Roundtrip-Smoke-Test erstellen

Status: erledigt.

```text
examples/log-senders/curl/roundtrip-smoke-test.sh
```

### EX-005: PHP-Logging-Client erstellen

Status: erledigt.

```text
examples/log-senders/php/post-json-log.php
examples/log-senders/php/post-text-log.php
examples/log-senders/php/post-error-log.php
examples/log-senders/php/roundtrip-smoke-test.php
examples/log-senders/php/src/LogSinkClient.php
```

### EX-005a: PHP-Reader-Beispiel ergänzen

Status: erledigt.

```text
examples/log-readers/php/get-latest-logs.php
```

### EX-006: Java-Logging-Client erstellen

Status: erledigt.

```text
examples/log-senders/java/pom.xml
examples/log-senders/java/src/main/java/de/sasd/logsink/examples/sender/HttpResult.java
examples/log-senders/java/src/main/java/de/sasd/logsink/examples/sender/LogSinkHttpClient.java
examples/log-senders/java/src/main/java/de/sasd/logsink/examples/sender/PostJsonLog.java
examples/log-senders/java/src/main/java/de/sasd/logsink/examples/sender/PostTextLog.java
examples/log-senders/java/src/main/java/de/sasd/logsink/examples/sender/PostErrorLog.java
examples/log-senders/java/src/main/java/de/sasd/logsink/examples/sender/RoundtripSmokeTest.java
```

### EX-007: C#-Beispiele erstellen

Status: erledigt.

```text
examples/csharp/LogSink.CSharpExamples.csproj
examples/csharp/Program.cs
```

Befehle:

```bash
dotnet run --project examples/csharp -- send-json
dotnet run --project examples/csharp -- send-text
dotnet run --project examples/csharp -- send-error
dotnet run --project examples/csharp -- read 10
dotnet run --project examples/csharp -- roundtrip
```

### EX-008: Sender-Clients an Authentifizierung anpassen

Status: offen.

Sobald Bearer-Tokens eingeführt werden, müssen curl, PHP, Java und C# angepasst werden.

## Bisherige Testdaten

```text
1-10    SQL-Demo-Daten
11-12   curl-Sender und curl-Roundtrip
13-16   PHP-Sender und PHP-Roundtrip
17-20   Java-Sender und Java-Roundtrip
21-24   C#-Sender und C#-Roundtrip
```

## Teststrategie

### Positivtest

1. Sender sendet Testmeldung.
2. Service antwortet erfolgreich.
3. Reader liest die letzten Logs.
4. Java-Viewer zeigt die neue Meldung.
5. Roundtrip-Test findet eine eindeutige `runId`.

### Negativtests

```text
falsche URL
falscher Content-Type
leerer Body
ungültiges JSON
zu großer Body
fehlender Token
falscher Token
Token ohne passenden Scope
```

## Sicherheitshinweise

Die aktuelle V0/V1 ist weiterhin ungeschützt. Die Beispiele sind deshalb nur für den Lern- und Testbetrieb geeignet.

Spätere Client-Versionen müssen mindestens unterstützen:

```text
Authorization: Bearer <token>
Source-Principal
Scope events.ingest
Timeouts
Fehlerbehandlung ohne Geheimnis-Ausgabe
```

Außerdem sollten Demo-Logmeldungen keine lokalen Pfade, Zugangsdaten, Tokens oder produktiven personenbezogenen Daten enthalten.

## Warum C++ noch nicht?

C++ bleibt interessant, wird aber bewusst zurückgestellt. Für C++ müsste zuerst entschieden werden:

```text
libcurl oder Boost.Beast?
CMake oder anderes Buildsystem?
vcpkg oder Systempakete?
Windows/Linux/macOS?
```

Das ist ein eigenes Lernfeld. C++ wird sinnvoller, wenn der API-Vertrag stabiler ist.

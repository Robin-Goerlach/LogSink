# C# LogSink Examples

Dieser Ordner enthält einfache C#-Beispiele für den aktuellen LogSink-V0/V1-Service.

## Zweck

Die Beispiele zeigen, wie eine C#/.NET-Anwendung Logmeldungen an LogSink senden und die letzten Meldungen wieder lesen kann.

Aktueller Flow:

```text
C#-Sender -> HTTP POST -> LogSink PHP-Service -> MariaDB
```

Reader/Roundtrip:

```text
C#-Sender -> Service -> Datenbank -> GET-API -> C#-Reader
```

## Voraussetzungen

- .NET SDK
- Netzwerkzugriff auf den LogSink-Service

Prüfen:

```bash
dotnet --version
```

## Konfiguration

Die Beispiele verwenden die Umgebungsvariable `LOGSINK_URL`.

Aktueller IONOS-Testbetrieb:

```bash
export LOGSINK_URL="http://api.sasd.de/logsink/index.php"
```

Lokale Entwicklung:

```bash
export LOGSINK_URL="http://127.0.0.1:8080/api/logs"
```

## Bauen

```bash
dotnet build examples/csharp/LogSink.CSharpExamples.csproj
```

## Ausführen

```bash
dotnet run --project examples/csharp -- send-json
dotnet run --project examples/csharp -- send-text
dotnet run --project examples/csharp -- send-error
dotnet run --project examples/csharp -- read 10
dotnet run --project examples/csharp -- roundtrip
```

## Befehle

| Befehl | Bedeutung |
|---|---|
| `send-json` | sendet eine JSON-Logmeldung |
| `send-text` | sendet eine Text-Logmeldung |
| `send-error` | sendet eine JSON-Fehlermeldung |
| `read 10` | liest die letzten 10 Meldungen |
| `roundtrip` | sendet eine eindeutige Meldung und prüft, ob sie wieder lesbar ist |

## Warum ein gemeinsames Projekt?

Für C# ist ein gemeinsames Konsolenprojekt zunächst einfacher als viele Einzelprojekte. Dadurch gibt es nur eine `.csproj`-Datei und einen Einstiegspunkt.

## Sicherheitshinweis

Die aktuelle V0/V1-API ist ungeschützt. Später muss dieses Beispiel erweitert werden um:

- Bearer-Token,
- Source-Principal,
- Scope `events.ingest`,
- Timeouts,
- robuste Fehlerbehandlung.

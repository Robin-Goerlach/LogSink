# LS-021: Request-ID einführen

## Ziel

Jeder HTTP-Request bekommt eine eindeutige serverseitige Request-ID.

Diese Request-ID wird sichtbar in:

```text
HTTP-Header: X-Request-ID
JSON-Antwort: requestId
Service-Log: requestId=...
```

## Warum?

Eine Request-ID ist ein technisches Aktenzeichen für genau eine HTTP-Anfrage.

Sie hilft bei Fragen wie:

```text
Welche Antwort gehört zu welchem Service-Log?
Welcher Fehler gehört zu welchem curl-Test?
Welche Datenbankaktion gehört zu welchem Request?
Welche Anfrage hat eine 500-Antwort verursacht?
```

## Sicherheitsnutzen

Eine Request-ID ist keine Authentifizierung und ersetzt keinen Token.

Sie verbessert aber die Sicherheit indirekt:

```text
bessere Nachvollziehbarkeit
bessere Fehleranalyse ohne Detailausgabe an Clients
bessere Vorbereitung für Audit-Logging
bessere Vorbereitung für Monitoring und Incident Response
```

Später kann ein Benutzer oder Admin bei einem Fehler nur die Request-ID melden. Die technischen Details bleiben im Server-Log.

## Bewusste Begrenzung in LS-021

LS-021 ändert noch nicht:

```text
Datenbankschema
Routing
Client-Konfiguration
Authentifizierung
Autorisierung
Scopes
Audit-Tabellen
```

Der Schritt bleibt klein, damit er leicht getestet und verstanden werden kann.

## Implementierung

### App.php

`App` erzeugt beim Start eines Requests eine neue Request-ID:

```text
req_ + 32 hexadezimale Zufallszeichen
```

Beispiel:

```text
req_0f6d4c0f7a7c4980a1c02ef8a0d0c4ff
```

Die ID wird automatisch in jede JSON-Antwort eingefügt.

### ServiceLogger.php

`ServiceLogger` kann nun optional eine Request-ID entgegennehmen und schreibt sie in die Logzeile:

```text
[2026-04-30 09:15:00] INFO  requestId=req_... Latest log entries requested: limit=5
```

## Testplan

### Syntax

```bash
php -l services/log-sink/src/App.php
php -l services/log-sink/src/ServiceLogger.php
```

### GET-Test

```bash
curl -i "http://api.sasd.de/logsink/index.php?limit=1"
```

Erwartet:

```text
X-Request-ID: req_...
```

und im JSON:

```json
{
  "status": "ok",
  "requestId": "req_...",
  "items": []
}
```

### POST-Test

```bash
curl -i -X POST "http://api.sasd.de/logsink/index.php" \
  -H "Content-Type: text/plain; charset=utf-8" \
  -H "User-Agent: SASD-LS-021-Test/0.1" \
  --data-binary "LS-021 Request-ID test"
```

Erwartet:

```json
{
  "status": "created",
  "requestId": "req_...",
  "id": 25
}
```

### Service-Log prüfen

```bash
grep "requestId=" services/log-sink/var/log/service.log | tail
```

## Erwartete Client-Auswirkung

Die bestehenden Beispiel-Clients sollten weiter funktionieren.

Der Java-Viewer sollte weiter funktionieren, weil der Jackson-Client unbekannte JSON-Felder ignoriert. Das ist wichtig, weil der GET-Response jetzt zusätzlich `requestId` enthält.

## Nächster Schritt

Nach erfolgreichem LS-021 ist der nächste logische Schritt:

```text
LS-022: Einheitliches JSON-Antwortmodell einführen
```

Dann wird aus:

```json
{
  "status": "ok",
  "requestId": "req_...",
  "items": []
}
```

später eher:

```json
{
  "ok": true,
  "requestId": "req_...",
  "data": {
    "items": []
  }
}
```

# LS-021 erklärt: Request-ID in LogSink

## Einleitung

Mit LS-021 haben wir keinen großen Architekturumbau vorgenommen. Wir haben keine neue Datenbanktabelle angelegt, keine Authentifizierung eingeführt und keinen neuen API-Endpunkt gebaut. Auf den ersten Blick wirkt der Schritt deshalb vielleicht kleiner, als er tatsächlich ist.

Trotzdem ist LS-021 ein wichtiger professioneller Schritt. Der Service beginnt damit, jeden HTTP-Request eindeutig zu kennzeichnen. Diese Kennzeichnung nennen wir Request-ID. Man kann sie sich wie ein Aktenzeichen vorstellen, das der Server für genau eine eingehende Anfrage vergibt.

Wenn ein Client eine Anfrage an LogSink sendet, erzeugt der PHP-Service nun eine solche ID. Diese ID wird in der HTTP-Antwort als Header zurückgegeben, zusätzlich in die JSON-Antwort geschrieben und im internen Service-Log verwendet. Damit kann man später eine Antwort, eine Fehlermeldung und einen technischen Logeintrag miteinander verbinden.

Wichtig ist: Eine Request-ID ist keine Anmeldung, kein Passwort, kein Token und keine Firewall. Sie verhindert keinen Angriff. Sie sorgt aber dafür, dass man bei Fehlern und später auch bei Sicherheitsereignissen besser nachvollziehen kann, was passiert ist.

## Was bedeutet Request-ID?

Eine Request-ID ist eine eindeutige Kennung für eine einzelne HTTP-Anfrage.

Wenn du im Browser oder mit curl diese URL aufrufst:

```text
http://api.sasd.de/logsink/index.php?limit=1
```

dann ist das ein HTTP-Request. Wenn du danach dieselbe URL noch einmal aufrufst, ist das ein zweiter HTTP-Request. Beide bekommen unterschiedliche Request-IDs.

Ein Beispiel:

```text
X-Request-ID: req_fbc09f3ee911b5954c34bbe935201ec1
```

Diese ID gehört nicht dauerhaft zu einem Benutzer, nicht dauerhaft zu einem Client und nicht dauerhaft zu einem Programm. Sie gehört nur zu genau dieser einen Anfrage.

Wenn danach ein POST gesendet wird, bekommt dieser POST wieder eine neue ID:

```text
X-Request-ID: req_e1ab2a711ef05a56824a2f064fbb304a
```

Das ist wichtig zu verstehen. Die Request-ID verbindet nicht automatisch mehrere fachlich zusammenhängende Vorgänge über längere Zeit. Dafür gäbe es später andere Konzepte wie Correlation-ID, Trace-ID, Session-ID, User-ID oder Source-ID. Für LS-021 bleiben wir bewusst klein: Eine Request-ID markiert genau einen Request.

## Was wurde in App.php geändert?

Die wichtigste Änderung in `App.php` ist, dass die Klasse jetzt beim Erzeugen des App-Objekts eine Request-ID erzeugt.

Vereinfacht passiert im Konstruktor jetzt Folgendes:

```php
$this->requestId = self::generateRequestId();
```

Das bedeutet: Sobald der Service für einen Request gestartet wird, bekommt dieser Request sein eigenes Aktenzeichen.

Die Methode `generateRequestId()` erzeugt dafür eine ID mit dem Präfix `req_` und zufälligen hexadezimalen Zeichen. Ein Beispiel sieht so aus:

```text
req_fbc09f3ee911b5954c34bbe935201ec1
```

Der Server erzeugt diese ID selbst. Das ist absichtlich so. Man könnte später erlauben, dass ein Client eine eigene `X-Request-ID` mitschickt. Für den Anfang wäre das aber unnötig kompliziert und kann auch problematisch sein, wenn Clients unsinnige, extrem lange oder manipulierte Werte schicken. Deshalb gilt in LS-021: Der Server erzeugt die Request-ID selbst und vertraut nicht auf einen Client-Wert.

Die zweite wichtige Änderung liegt in der Methode, die JSON-Antworten ausgibt. Dort wird jetzt zusätzlich ein HTTP-Header gesetzt:

```php
header('X-Request-ID: ' . $this->requestId);
```

Dadurch sieht man die Request-ID direkt in der HTTP-Antwort. Bei deinem Test war das zum Beispiel:

```text
X-Request-ID: req_fbc09f3ee911b5954c34bbe935201ec1
```

Die dritte Änderung ist, dass die Request-ID auch in den JSON-Body geschrieben wird. Aus einer alten Antwort wie:

```json
{
  "status": "ok",
  "items": []
}
```

wird nun:

```json
{
  "status": "ok",
  "requestId": "req_fbc09f3ee911b5954c34bbe935201ec1",
  "items": []
}
```

Das ist für Menschen angenehm, weil man die Request-ID auch dann sieht, wenn man nicht auf die HTTP-Header schaut. Viele einfache Clients, Browser-Plugins oder Debug-Ausgaben zeigen den Body deutlicher als die Header.

Dabei achtet die neue Hilfsmethode darauf, dass `status` weiterhin ganz oben bleibt. Das ist kein technisches Muss, aber es macht die Antwort lesbarer. Der Mensch sieht zuerst, ob die Antwort `ok`, `created` oder `error` ist, und direkt danach die Request-ID.

## Was wurde im ServiceLogger geändert?

Der `ServiceLogger` schreibt technische Meldungen des PHP-Services in die Datei `var/log/service.log`. Vor LS-021 konnte er im Wesentlichen eine normale Info- oder Fehlermeldung schreiben. Zum Beispiel sinngemäß:

```text
[2026-04-30 07:03:46] INFO  Log entry written with id 25
```

Das ist bereits nützlich, aber noch nicht eindeutig genug. Wenn mehrere Requests kurz hintereinander kommen, sieht man zwar einzelne Logzeilen, aber man kann nicht immer sicher sagen, welche Zeile zu welcher HTTP-Antwort gehört.

Deshalb wurde `ServiceLogger` erweitert. Die Methoden `info()` und `error()` können nun zusätzlich eine Request-ID erhalten.

Vorher war die Idee ungefähr:

```php
$this->logger->info('Log entry written with id 25');
```

Jetzt kann `App.php` schreiben:

```php
$this->logger->info(
    'Log entry written with id 25',
    $this->requestId
);
```

Der Logger baut daraus eine Logzeile, in der die Request-ID sichtbar ist:

```text
[2026-04-30 07:03:46] INFO  requestId=req_e1ab2a711ef05a56824a2f064fbb304a Log entry written with id 25
```

Das gleiche gilt für Fehler. `error()` kann weiterhin eine Exception entgegennehmen, aber zusätzlich auch die Request-ID. Dadurch kann ein Fehler im Service-Log später genau einer HTTP-Antwort zugeordnet werden.

Die eigentliche technische Änderung im Logger ist klein. Intern wird geprüft, ob eine Request-ID übergeben wurde. Wenn ja, wird ein Textbaustein erzeugt:

```text
requestId=req_...
```

Dieser Textbaustein wird in die Logzeile eingefügt.

Wichtig ist auch: Die Änderung bleibt rückwärtsverträglich. Alte Aufrufe wie `info($message)` funktionieren weiterhin. Auch `error($message, $exception)` funktioniert weiterhin. Die Request-ID ist ein optionaler Zusatz.

## Warum wird die Request-ID nicht sofort in der Datenbank gespeichert?

Das ist eine sehr gute Frage, weil sie auf den ersten Blick widersprüchlich wirkt. Wenn die Request-ID so nützlich ist, warum speichern wir sie dann nicht direkt in `log_entries`?

Die Antwort ist: Weil LS-021 bewusst klein bleiben soll.

Im Moment speichert die Tabelle `log_entries` vor allem die eigentliche Logmeldung, die ein Client an den Service sendet. Sie speichert also den Inhalt des POST-Requests und einige technische Daten wie IP-Adresse, HTTP-Methode, Request-URI, Content-Type und User-Agent.

Die Request-ID beschreibt aber nicht die Logmeldung selbst, sondern den HTTP-Vorgang, bei dem diese Logmeldung angekommen ist.

Das ist ein kleiner, aber wichtiger Unterschied.

Bei einem POST-Request gibt es beides:

```text
HTTP-Request:    Der Client sendet etwas an den Service.
Logeintrag:      Der Service speichert den Body als Logmeldung.
```

Die Request-ID gehört zunächst zum HTTP-Request. Der gespeicherte Logeintrag hat bereits eine Datenbank-ID, zum Beispiel:

```text
id = 25
```

In deinem POST-Test hast du beides gesehen:

```json
{
  "status": "created",
  "requestId": "req_e1ab2a711ef05a56824a2f064fbb304a",
  "id": 25
}
```

Damit kannst du bereits eine Kette bilden:

```text
Client-Antwort enthält requestId=req_e1ab... und id=25.
Service-Log enthält requestId=req_e1ab... und "Log entry written with id 25".
Datenbank enthält den eigentlichen Logeintrag mit id=25.
```

Die Request-ID muss also für diesen ersten Schritt nicht zwingend in der Datenbank stehen. Sie verbindet bereits die HTTP-Antwort mit dem Service-Log. Und über die zurückgegebene `id` verbindet sie den Schreibvorgang indirekt auch mit dem gespeicherten Logeintrag.

Bei einem GET-Request ist es sogar noch klarer: Ein GET liest nur Daten. Er erzeugt keinen neuen Logeintrag in `log_entries`. Trotzdem ist eine Request-ID nützlich, weil ein GET fehlschlagen kann. Wenn der Java-Viewer später eine Fehlermeldung bekommt und diese Request-ID anzeigt, kannst du im Service-Log genau nach dieser ID suchen.

Für LS-021 wollen wir also zuerst Debugging und Nachvollziehbarkeit verbessern, ohne das Datenbankschema zu ändern.

## Wann sollte die Request-ID in die Datenbank?

Später kann es sehr sinnvoll sein, die Request-ID auch in der Datenbank zu speichern. Aber wahrscheinlich nicht einfach nur als neue Spalte in `log_entries`, sondern sauberer über eine eigene technische Request- oder Audit-Tabelle.

Zum Beispiel könnte es später eine Tabelle geben:

```text
ingest_requests
```

Darin könnte stehen:

```text
request_id
received_at
source_ip
http_method
request_uri
response_status
principal_id
token_id
error_code
```

Und die eigentlichen Logevents könnten dann darauf verweisen.

Oder wir ergänzen in einer Zwischenstufe eine Spalte in `log_entries`, wenn wir bewusst einfach bleiben wollen:

```text
request_id
```

Beides ist möglich. Aber beides ist ein eigener Schritt, weil es eine Datenbankmigration, SQL-Anpassungen, Repository-Anpassungen und Tests erfordert.

Wenn wir das in LS-021 sofort mitgemacht hätten, wäre aus einem kleinen, gut verständlichen Schritt plötzlich ein größerer Umbau geworden. Deshalb ist es didaktisch besser, zuerst die Request-ID im Service einzuführen und sie sichtbar zu machen. Danach können wir entscheiden, wie sie dauerhaft gespeichert werden soll.

## Wofür nutzt man die Request-ID ohne Datenbankspalte?

Auch ohne Datenbankspalte ist sie sofort nützlich.

Stell dir vor, ein Benutzer ruft den Service auf und bekommt eine Fehlermeldung:

```json
{
  "status": "error",
  "requestId": "req_abc123...",
  "error": "internal_server_error",
  "message": "Internal server error."
}
```

Der Benutzer sieht keine technischen Details. Das ist gut, denn technische Details können interne Pfade, SQL-Fehler, Zugangsnamen oder andere sensible Informationen verraten.

Der Benutzer kann aber sagen:

```text
Ich hatte einen Fehler mit Request-ID req_abc123.
```

Dann sucht der Administrator im Service-Log:

```bash
grep "req_abc123" services/log-sink/var/log/service.log
```

Dort findet er die echte technische Fehlermeldung. Dadurch bleibt die Fehlersuche möglich, ohne dass man sensible Details an den Client ausliefern muss.

Das ist ein typischer Sicherheitsgewinn. Nicht, weil die Request-ID einen Angriff blockiert, sondern weil sie uns erlaubt, nach außen weniger preiszugeben und intern trotzdem sauber zu analysieren.

Auch bei erfolgreichen Requests hilft sie. Wenn ein POST erfolgreich war, kann der Service-Logeintrag ungefähr sagen:

```text
requestId=req_e1ab... Log entry written with id 25
```

Die HTTP-Antwort sagt ebenfalls:

```text
requestId=req_e1ab...
id=25
```

So weißt du: Diese Antwort, dieser Service-Logeintrag und dieser Datenbankeintrag gehören zu demselben Vorgang.

## Request-ID, Log-ID und Zusammenhang

Es ist hilfreich, die verschiedenen IDs auseinanderzuhalten.

Die Request-ID gehört zur HTTP-Anfrage:

```text
requestId=req_e1ab...
```

Die Datenbank-ID gehört zum gespeicherten Logeintrag:

```text
id=25
```

Ein POST-Request kann einen Logeintrag erzeugen. Dann hängen Request-ID und Datenbank-ID zusammen, aber sie meinen nicht dasselbe.

Ein GET-Request erzeugt normalerweise keinen neuen Logeintrag in `log_entries`. Er hat trotzdem eine Request-ID, weil auch ein Lesevorgang nachvollziehbar sein soll.

Später können noch weitere IDs dazukommen:

```text
sourceId       Welche Anwendung sendet?
principalId    Welche technische Identität wurde authentifiziert?
correlationId  Welcher größere Vorgang verbindet mehrere Requests?
traceId        Welche verteilte Ablaufverfolgung läuft über mehrere Systeme?
```

Für den Anfang reicht aber:

```text
Request-ID = Aktenzeichen für eine HTTP-Anfrage
Log-ID     = Datenbank-ID eines gespeicherten Logeintrags
```

## Warum ist das ein Sicherheitsbaustein?

Sicherheit besteht nicht nur aus Login und Passwort. Sicherheit besteht auch aus Nachvollziehbarkeit, Fehlerkontrolle und der Fähigkeit, Vorfälle zu untersuchen.

Ein unsicherer Service verrät bei Fehlern vielleicht zu viel:

```text
SQLSTATE ...
/homepages/12/...
DB_USERNAME ...
Stacktrace ...
```

Ein besserer Service sagt nach außen nur:

```text
Internal server error.
Request-ID: req_...
```

Die Details bleiben im Service-Log.

Dadurch wird weniger Information preisgegeben, aber die interne Analyse bleibt möglich. Genau das ist ein professionelles Sicherheitsmuster.

Außerdem hilft die Request-ID später beim Erkennen von Mustern. Wenn wir später Authentifizierung einführen, könnten Service-Logs ungefähr so aussehen:

```text
requestId=req_1 Token missing
requestId=req_2 Token invalid
requestId=req_3 Scope events.ingest missing
```

Dann kann man systematisch auswerten, was passiert.

## Was LS-021 noch nicht löst

LS-021 schützt den offenen Endpunkt noch nicht.

Auch nach LS-021 kann grundsätzlich jeder, der die URL kennt, einen POST senden, solange keine Authentifizierung eingebaut ist. Die Request-ID verhindert das nicht.

LS-021 löst auch noch nicht das Problem, dass die Datenbank keine technische Request-Historie hat. Dafür brauchen wir später entweder eine Spalte `request_id` oder besser eine eigene Request-/Audit-Tabelle.

LS-021 löst auch noch kein Routing, keine einheitliche API-Struktur und keine JSON-Validierung.

Das ist aber in Ordnung. Der Schritt ist genau deshalb wertvoll, weil er klein ist und trotzdem sofort eine professionelle Grundlage schafft.

## Fazit

Mit LS-021 bekommt jeder Request ein Aktenzeichen. Dieses Aktenzeichen erscheint im Header, im JSON und im Service-Log.

Dadurch können wir Fehler besser verfolgen, ohne interne Details an Clients auszugeben. Wir können Antworten und technische Logzeilen miteinander verbinden. Bei POST-Requests können wir über die zurückgegebene Datenbank-ID zusätzlich den gespeicherten Logeintrag zuordnen.

Die Request-ID ist also kein Schloss an der Tür. Sie ist eher das Aktenzeichen auf jeder Vorgangsmappe. Ohne dieses Aktenzeichen kann man zwar arbeiten, aber sobald mehrere Vorgänge gleichzeitig passieren oder ein Fehler auftritt, wird die Suche mühsam. Mit Aktenzeichen wird der Service nachvollziehbarer, professioneller und besser vorbereitet für die nächsten Sicherheitsstufen.

Der nächste logische Schritt ist danach `LS-022`: ein einheitliches JSON-Antwortmodell. Dann bekommt jede Antwort eine noch klarere Struktur, und die Request-ID wird Teil eines allgemeinen Antwortschemas.

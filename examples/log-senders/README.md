# Log Sender Examples

Dieser Ordner enthält Beispiele, die Logmeldungen an den LogSink-Service senden.

## Aktueller Zweck

Die Sender testen die Schreibseite des Services:

```text
Sender -> HTTP POST -> PHP-Service -> MariaDB
```

Danach kann man mit einem Reader oder dem Java-Viewer prüfen:

```text
MariaDB -> HTTP GET -> Reader/Java-Viewer
```

## Aktuelle Beispiele

```text
curl/
```

curl ist der erste Schritt, weil man damit schnell und sichtbar testen kann, ohne vorher eine Programmiersprache oder Build-Umgebung einzurichten.

## Spätere Beispiele

Geplant:

```text
php/
java/
node/
dotnet/
```

Diese Beispiele sollen mit dem Service mitwachsen.

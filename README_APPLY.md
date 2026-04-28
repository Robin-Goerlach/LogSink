# LogSink commented code V1

Dieses Paket enthält nur kommentierte Code-Dateien für den aktuellen V1-Stand.

## Ziel

Der Commit soll ein reiner Verständnis-Commit sein:

- Kommentare ergänzen
- Einstiegspunkte erklären
- Datenfluss erklären
- typische Stolperfallen markieren
- keine neue Funktion einführen
- keine API ändern
- keine Datenbank ändern

## Enthaltene Dateien

```text
services/log-sink/index.php
services/log-sink/public/index.php
services/log-sink/src/App.php
services/log-sink/src/Bootstrap.php
services/log-sink/src/Config.php
services/log-sink/src/Database.php
services/log-sink/src/LogRepository.php
services/log-sink/src/ServiceLogger.php

clients/java-log-viewer/src/main/java/de/sasd/logsink/viewer/Main.java
clients/java-log-viewer/src/main/java/de/sasd/logsink/viewer/client/LogServiceClient.java
clients/java-log-viewer/src/main/java/de/sasd/logsink/viewer/model/LogEntry.java
clients/java-log-viewer/src/main/java/de/sasd/logsink/viewer/model/LogResponse.java
clients/java-log-viewer/src/main/java/de/sasd/logsink/viewer/ui/LogDetailDialog.java
clients/java-log-viewer/src/main/java/de/sasd/logsink/viewer/ui/LogTableModel.java
clients/java-log-viewer/src/main/java/de/sasd/logsink/viewer/ui/LogViewerFrame.java
clients/java-log-viewer/src/main/java/de/sasd/logsink/viewer/ui/SimpleDocumentListener.java
```

## Einspielen

Im Root des Repositories:

```bash
git status
git switch -c chore/comment-current-v1-code

unzip -o LogSink_commented_code_v1.zip -d .

git status
git diff --stat
git diff
```

## Prüfen

PHP-Syntax prüfen:

```bash
php -l services/log-sink/index.php
php -l services/log-sink/public/index.php
php -l services/log-sink/src/App.php
php -l services/log-sink/src/Bootstrap.php
php -l services/log-sink/src/Config.php
php -l services/log-sink/src/Database.php
php -l services/log-sink/src/LogRepository.php
php -l services/log-sink/src/ServiceLogger.php
```

Java bauen:

```bash
mvn -f clients/java-log-viewer/pom.xml clean package
```

Remote-Service prüfen:

```bash
curl -i "http://api.sasd.de/logsink/index.php?limit=5"
```

## Commit

```bash
git add services/log-sink clients/java-log-viewer/src
git commit -m "Add explanatory comments to current V1 code"
git push -u origin chore/comment-current-v1-code
```

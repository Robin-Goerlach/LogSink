# Entwicklungshinweise

## Lokale Entwicklung

PHP-Service starten:

```bash
./scripts/start-php-service.sh
```

Java-Viewer bauen und starten:

```bash
./scripts/start-java-viewer.sh
```

Smoke-Test ausführen:

```bash
./scripts/smoke-test.sh
```

## PHP-Service

Der Service verwendet aktuell keinen Composer und kein externes Framework. Das ist bewusst so, damit die erste Version klein und nachvollziehbar bleibt.

Die wichtigsten Dateien:

```text
services/log-sink/public/index.php
services/log-sink/src/Bootstrap.php
services/log-sink/src/App.php
services/log-sink/src/LogRepository.php
services/log-sink/src/Database.php
```

## Java-Viewer

Der Java-Viewer ist ein Maven-Projekt und verwendet Java Swing.

```bash
cd clients/java-log-viewer
mvn clean package
```

## Git-Konvention

Empfohlene Commit-Message für den aktuellen Strukturumbau:

```text
Restructure LogSink as service/client monorepo
```

## Namenskonvention

- Ordnernamen kleinschreiben.
- Mehrwortnamen mit Bindestrich trennen.
- PHP-Service: `services/log-sink`
- Java-Viewer: `clients/java-log-viewer`

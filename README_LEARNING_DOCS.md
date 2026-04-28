# LogSink Learning Documents

Diese Datei ist der Einstieg in die Lern- und Projektdokumentation für LogSink.

Die Dokumentation soll helfen, über mehrere Arbeitstage hinweg nicht den roten Faden zu verlieren. Sie beschreibt nicht nur den Zielzustand, sondern auch den Weg vom bewusst einfachen und ungeschützten Prototyp zu einem besser strukturierten und später abgesicherten Logging-Service.

## Aktueller Stand

Der aktuelle V0/V1-Service läuft remote bei IONOS über:

```text
http://api.sasd.de/logsink/index.php
```

Der Service kann Logmeldungen schreiben und lesen. Die echte Konfiguration wurde aus der Browser-Reichweite verschoben. Die HTTP-API ist aber weiterhin ungeschützt und wird erst in späteren LS-Schritten abgesichert.

Die Client-Beispielrunde für den ungeschützten V0/V1-Stand ist abgeschlossen. Es existieren Beispiele für:

```text
curl
PHP
Java
C#
```

Diese Beispiele testen den kompletten Flow:

```text
Sender -> HTTP POST -> PHP-Service -> MariaDB -> GET-API -> Reader/Java-Viewer
```

Der aktuelle Testbestand umfasst 24 nachvollziehbare Logmeldungen:

```text
1-10    SQL-Demo-Daten
11-12   curl-Sender und curl-Roundtrip
13-16   PHP-Sender und PHP-Roundtrip
17-20   Java-Sender und Java-Roundtrip
21-24   C#-Sender und C#-Roundtrip
```

## Wichtige Dokumente

```text
docs/learning/
├── 00-project-brief.md
├── 01-source-analysis.md
├── 02-gap-analysis.md
├── 03-learning-plan.md
├── 03a-learning-plan-addendum-2026-04-27.md
├── 04-implementation-roadmap.md
├── 05-decision-log.md
├── 06-session-state.md
├── 07-beginner-guide.md
├── 08-test-and-quality-plan.md
├── 09-risk-register.md
├── 10-from-unprotected-to-secure.md
├── 11-v1-code-walkthrough-and-first-hardening.md
├── 12-logging-client-plan.md
├── 14-ionos-deployment-notes.md
├── 15-java-client-configuration-plan.md
├── 16-code-commenting-plan.md
├── 99-open-questions.md
└── templates/
    └── step-template.md
```

## Weitere zentrale Dateien

```text
TODO.md
CHANGELOG.md
README.md
README_APPLY.md
contracts/http-api/logs-v1.md
database/mariadb/README.md
services/log-sink/README.md
clients/java-log-viewer/README.md
```

## Beispiel-Clients

Die Beispiele liegen bewusst unter `examples/`, weil sie Lern-, Test- und Demonstrationscode sind.

```text
examples/
├── csharp/
├── log-readers/
│   ├── curl/
│   └── php/
└── log-senders/
    ├── curl/
    ├── java/
    └── php/
```

## Nächste sinnvolle Schritte

Die Client-Beispielrunde und die dazugehörige Dokumentationskonsolidierung sind abgeschlossen.

Als nächstes beginnt die eigentliche Verbesserung des Services:

```text
LS-021: Request-ID einführen
LS-022: Einheitliches JSON-Antwortmodell einführen
LS-023: Einfaches Routing einführen
LS-024: Front-Controller-Route-Parameter ergänzen
```

Danach folgen strukturierte Events, Authentifizierung, Autorisierung, Scopes, Audit-Logging und robustere Tests.

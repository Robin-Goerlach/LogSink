# LogSink Learning Area

Dieser Ordner hält den roten Faden für die Weiterentwicklung von SASD LogSink.

## Zweck

LogSink ist aktuell eine bewusst einfache und noch ungeschützte V0/V1. Ziel ist nicht, möglichst schnell viele Funktionen einzubauen, sondern das System in kleinen, verständlichen und testbaren Schritten zu einem sicheren, dokumentierten und wartbaren Logging-Service auszubauen.

Der Ordner `docs/learning` ist das Projektgedächtnis. Er beantwortet nicht nur, **was** wir bauen, sondern auch **warum** und **in welcher Reihenfolge**.

## Aktueller Stand

Der aktuelle Service läuft bei IONOS über:

```text
http://api.sasd.de/logsink/index.php
```

Der Service kann Logmeldungen schreiben und lesen. Die echte Konfiguration liegt nicht mehr im öffentlich erreichbaren Service-Verzeichnis. Die HTTP-API selbst ist aber weiterhin ungeschützt.

Die V0/V1-Client-Beispielrunde ist abgeschlossen:

```text
curl
PHP
Java
C#
```

Der nächste Umsetzungsschritt ist:

```text
LS-021: Request-ID einführen
```

## Dateien

| Datei | Zweck |
|---|---|
| `00-project-brief.md` | Projektziel, Grundregeln und didaktischer Rahmen |
| `01-source-analysis.md` | Zusammenfassung der bereitgestellten Mustralla/MustelaLogAPI-Dokumente |
| `02-gap-analysis.md` | Vergleich: aktueller LogSink-Stand vs. Zielbild |
| `03-learning-plan.md` | Haupt-Lehrplan mit atomaren Schritten |
| `03a-learning-plan-addendum-2026-04-27.md` | Ergänzende LS-Schritte aus Maven-, curl- und IONOS-Diagnose |
| `04-implementation-roadmap.md` | Technische Roadmap über Service, Datenbank und Client |
| `05-decision-log.md` | Architekturentscheidungen und ihre Begründung |
| `06-session-state.md` | Aktueller Arbeitsstand für die nächste Sitzung |
| `07-beginner-guide.md` | Praktische Erklärungen für Neulinge: Maven, Composer, curl, PHPUnit, phpDocumentor, Deployment |
| `08-test-and-quality-plan.md` | Teststrategie, curl-Tests, PHPUnit, Java-Client-Prüfung |
| `09-risk-register.md` | Risiken, Inkonsistenzen und technische Schulden |
| `10-from-unprotected-to-secure.md` | Sicherheits-Lernpfad von der offenen V1 zur geschützten API |
| `11-v1-code-walkthrough-and-first-hardening.md` | Ausführliche Erklärung der aktuellen V1, des Codes und der ersten Sicherungsmaßnahme |
| `12-logging-client-plan.md` | Plan und Status der schreibenden Logging-Clients und Reader-Beispiele |
| `14-ionos-deployment-notes.md` | Praktische IONOS-Deployment-Erkenntnisse für die aktuelle V1 |
| `15-java-client-configuration-plan.md` | Plan zur Ablösung der hart codierten Java-Service-URL durch Konfiguration |
| `16-code-commenting-plan.md` | Plan für erklärende Kommentare in Service- und Client-Code |
| `99-open-questions.md` | Offene Fragen und Klärungsbedarf |
| `templates/step-template.md` | Vorlage für jeden einzelnen Umsetzungsschritt |

## Arbeitsregel

Zu Beginn jeder neuen Arbeitssitzung:

```bash
git status
git log --oneline --decorate -5
```

Danach lesen:

1. `docs/learning/06-session-state.md`
2. `TODO.md`
3. bei Architekturfragen `docs/learning/05-decision-log.md`

Am Ende jeder Sitzung:

1. Session-State aktualisieren.
2. TODO aktualisieren.
3. Entscheidungen dokumentieren.
4. relevante Tests notieren.
5. Commit schreiben.

## Didaktische Regel

Jeder technische Schritt soll so klein sein, dass er verstanden, getestet und mit einer sinnvollen Commit-Message abgeschlossen werden kann.

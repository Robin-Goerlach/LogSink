# LogSink Learning Documents

Dieses Dokument ist der Einstiegspunkt in die projektbegleitende Lern- und Planungsdokumentation von **SASD LogSink**.

## Zweck

LogSink ist ein Lehrprojekt. Es beginnt mit einer bewusst einfachen und ungeschützten V1 und wird Schritt für Schritt zu einem sicheren, getesteten und gut dokumentierten Logging-Service ausgebaut.

Die Dokumentation hält fest:

- wo wir stehen,
- warum wir etwas entschieden haben,
- welche Risiken bekannt sind,
- welche Schritte als Nächstes kommen,
- wie Service, Datenbank und Clients gemeinsam weiterentwickelt werden.

## Wichtige Einstiegsdateien

Für den täglichen Start reichen meistens diese Dateien:

```text
docs/learning/06-session-state.md
TODO.md
CHANGELOG.md
docs/learning/05-decision-log.md
```

Empfohlener Start in einer neuen Sitzung:

```bash
git status
git pull --ff-only
git log --oneline --decorate -5
```

Danach lesen:

1. `docs/learning/06-session-state.md`
2. `TODO.md`
3. bei Architekturfragen `docs/learning/05-decision-log.md`

## Aktueller Projektstand

Stand: 2026-04-28

Erreicht:

- Monorepo-Struktur steht.
- PHP-Service liegt unter `services/log-sink`.
- Java-Viewer liegt unter `clients/java-log-viewer`.
- MariaDB-Skripte liegen unter `database/mariadb`.
- IONOS-V1 läuft und liefert JSON.
- Java-Viewer kann Remote-Logs anzeigen.
- `.env` liegt nicht mehr im öffentlich erreichbaren Service-Verzeichnis.
- externe `.env-logsink` wird unterstützt.
- Diagnose-Skript liegt unter `tools/diagnostics`.
- Code von Service und Viewer ist ausführlich kommentiert.
- Java-Viewer nutzt eine Konfigurationsdatei statt hart codierter Service-URL.
- schreibende Logging-Clients sind als nächster Projektbereich geplant.

Wichtig: Die HTTP-API ist weiterhin bewusst ungeschützt. Die bisherige Sicherungsmaßnahme betrifft vor allem die Konfigurationsdatei, nicht die API selbst.

## Dokumentationsstruktur

```text
docs/
├── architecture.md
├── development.md
├── repository-structure.md
└── learning/
    ├── README.md
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

## Kurzübersicht der Learning-Dokumente

| Datei | Zweck |
|---|---|
| `00-project-brief.md` | Projektziel, Grundregeln und didaktischer Rahmen |
| `01-source-analysis.md` | Zusammenfassung der bereitgestellten Mustralla/MustelaLogAPI-Dokumente |
| `02-gap-analysis.md` | Vergleich zwischen aktuellem LogSink-Stand und Zielbild |
| `03-learning-plan.md` | Haupt-Lehrplan mit atomaren Schritten |
| `03a-learning-plan-addendum-2026-04-27.md` | Ergänzende Schritte aus Maven-, curl- und IONOS-Diagnose |
| `04-implementation-roadmap.md` | technische Roadmap über Service, Datenbank und Client |
| `05-decision-log.md` | Architekturentscheidungen und ihre Begründung |
| `06-session-state.md` | aktueller Arbeitsstand und Einstiegspunkt für die nächste Sitzung |
| `07-beginner-guide.md` | praktische Erklärungen für Neulinge |
| `08-test-and-quality-plan.md` | Teststrategie und Qualitätsplan |
| `09-risk-register.md` | Risiken, Inkonsistenzen und technische Schulden |
| `10-from-unprotected-to-secure.md` | Sicherheits-Lernpfad von der offenen V1 zur geschützten API |
| `11-v1-code-walkthrough-and-first-hardening.md` | Erklärung der aktuellen V1 und der ersten Sicherungsmaßnahme |
| `12-logging-client-plan.md` | Plan für schreibende Logging-Clients und deren Tests |
| `14-ionos-deployment-notes.md` | praktische IONOS-Deployment-Erkenntnisse |
| `15-java-client-configuration-plan.md` | Plan und Umsetzungsidee für Java-Viewer-Konfiguration |
| `16-code-commenting-plan.md` | Plan für erklärende Kommentare in Service- und Client-Code |
| `99-open-questions.md` | offene Fragen und Klärungsbedarf |
| `templates/step-template.md` | Vorlage für neue Umsetzungsschritte |

## Wichtige Arbeitsregeln

### 1. Kleine Schritte

Jeder Schritt soll möglichst atomar sein:

- ein Ziel,
- eine nachvollziehbare Änderung,
- ein Test,
- eine Commit-Message.

### 2. Erst verstehen, dann umbauen

Vor größeren Umbauten prüfen wir:

- Was tut der aktuelle Code?
- Warum wurde er so gebaut?
- Welche Risiken hat er?
- Wie testen wir die Änderung?

### 3. Service, Datenbank und Clients gemeinsam denken

LogSink besteht nicht nur aus dem PHP-Service.

Wichtige Teile:

```text
database/mariadb        SQL, Schema, Demo-Daten
services/log-sink       PHP-Service
clients/java-log-viewer Java-Viewer
tools/diagnostics       Diagnosewerkzeuge
docs/learning           Projektgedächtnis
```

Später kommen schreibende Beispiel-Clients hinzu.

### 4. Keine Secrets ins Repository

Nicht committen:

```text
.env
.env-logsink
.env.IONOS
client-settings.json
```

Committen:

```text
.env.example
client-settings.example.json
```

## Aktuell erledigte wichtige Schritte

| Schritt | Status |
|---|---|
| LS-000 Repository prüfen | erledigt |
| LS-010 PHP-Service erreichen | erledigt |
| LS-011 MariaDB-Demo-Daten prüfen | erledigt |
| LS-012 curl-GET-Test | erledigt |
| LS-013 Java-Viewer bauen/starten | erledigt |
| LS-013a Maven-Build aus Repository-Root | erledigt |
| LS-013b Remote-Service mit curl diagnostizieren | erledigt |
| LS-016a IONOS-Dateistruktur verstehen | erledigt |
| LS-016b `.env` aus Browser-Reichweite entfernen | erledigt |
| LS-016c Diagnose-Skript kontrolliert verwenden | erledigt |
| LS-017 MariaDB-Skripte lokal/IONOS trennen | erledigt |
| LS-020 Code ausführlich kommentieren | erledigt |
| LS-019 Java-Viewer-Konfiguration einführen | erledigt |

## Nächste sinnvolle Schritte

Kurzfristig:

1. schreibende Logging-Clients planen und beginnen,
2. curl-Sender für aktuelle V1 erstellen,
3. PHP- oder Java-Sender erstellen,
4. Tests definieren: Sender -> Service -> DB -> Viewer,
5. danach API-Routing und Health-Endpunkt vorbereiten.

Noch offen:

- `docs/learning/10-git-workflows.md` erstellen.
- Ziel-API mit `index.php?route=/api/v1/...` einführen.
- standardisiertes JSON-Antwortmodell einführen.
- Bearer-Token, Principals und Scopes einführen.
- Datenbankmodell für strukturierte Events erweitern.

## Leitgedanke

LogSink soll nicht nur funktionieren. Es soll verständlich bleiben.

Die Dokumentation ist deshalb Teil des Projekts, nicht Beiwerk.

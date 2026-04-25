# LogSink Learning Area

Dieser Ordner hält den roten Faden für die Weiterentwicklung von SASD LogSink.

## Zweck

LogSink ist aktuell eine bewusst einfache und ungeschützte V1. Ziel ist nicht, möglichst schnell viele Funktionen einzubauen, sondern das System in kleinen, verständlichen und testbaren Schritten zu einem sicheren, dokumentierten und wartbaren Logging-Service auszubauen.

## Dateien

| Datei | Zweck |
|---|---|
| `00-project-brief.md` | Projektziel, Grundregeln und didaktischer Rahmen |
| `01-source-analysis.md` | Zusammenfassung der bereitgestellten Mustralla/MustelaLogAPI-Dokumente |
| `02-gap-analysis.md` | Vergleich: aktueller LogSink-Stand vs. Zielbild |
| `03-learning-plan.md` | Lehrplan mit atomaren Schritten |
| `04-implementation-roadmap.md` | technische Roadmap über Service, Datenbank und Client |
| `05-decision-log.md` | Architekturentscheidungen und ihre Begründung |
| `06-session-state.md` | aktueller Arbeitsstand für die nächste Sitzung |
| `07-beginner-guide.md` | praktische Erklärungen für Neulinge: Maven, Composer, curl, PHPUnit, phpDocumentor, Deployment |
| `08-test-and-quality-plan.md` | Teststrategie, curl-Tests, PHPUnit, Java-Client-Prüfung |
| `09-risk-register.md` | Risiken, Inkonsistenzen und technische Schulden |
| `99-open-questions.md` | offene Fragen und Klärungsbedarf |
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
4. Commit schreiben.

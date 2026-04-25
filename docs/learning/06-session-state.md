# Session State - SASD LogSink

## Aktueller Stand

Datum: 2026-04-25

Branch-Status zuletzt bekannt:

- `main` enthält die neue Monorepo-Struktur.
- Arbeitsbaum war nach Merge sauber.
- lokales `.env` und `service.log` dürfen untracked/ignored sein.

## Aktuelle Repository-Struktur

```text
LogSink/
├── clients/java-log-viewer
├── contracts/http-api
├── database/mariadb
├── docs
├── scripts
└── services/log-sink
```

## Zuletzt erledigt

- Repository-Struktur bereinigt.
- Java-Client in `clients/java-log-viewer` integriert.
- PHP-Service in `services/log-sink` eingeordnet.
- Mustralla/MustelaLogAPI-Dokumente bereitgestellt.
- Zielrichtung geklärt: ungeschützte V1 wird als Lernbasis genutzt.
- Learning-Dokumentationspaket erzeugt.

## Nächster sinnvoller Schritt

1. Dieses ZIP ins Repository einspielen.
2. Commit erstellen.
3. `docs/learning/01-source-analysis.md` gemeinsam prüfen.
4. Danach mit `LS-000` bis `LS-013` die aktuelle Basis reproduzierbar testen.
5. Erst dann mit API-Basis-Schritten ab `LS-020` beginnen.

## Offene Punkte

- Soll die alte `/api/logs`-Schnittstelle langfristig als Legacy erhalten bleiben?
- Soll der neue API-Vertrag als `logs-v1.md` aktualisiert oder als `logs-v1.1.md` daneben geführt werden?
- Welche IONOS-Umgebung ist später relevant: Shared Hosting, VPS oder Managed Server?
- Soll der Java-Client zuerst vollständig auf neue API umgestellt werden oder Legacy/API-v1 parallel unterstützen?
- Wie heißen die endgültigen DB-Tabellen für Credentials?
- Soll es später einen WPF-Client im selben Repository geben?

## Warnung für nächste Sitzung

Nicht direkt anfangen zu programmieren.

Zuerst:

```bash
git status
git log --oneline --decorate -5
```

Dann lesen:

1. `TODO.md`
2. `docs/learning/06-session-state.md`
3. `docs/learning/03-learning-plan.md`

## Merksatz

Wir bauen nicht schnell. Wir bauen nachvollziehbar.

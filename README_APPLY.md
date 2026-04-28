# LogSink Docs Update - Dienstag 2026-04-28

Dieses Paket bereinigt und ergänzt die Dokumentation für den nächsten Arbeitsschritt.

## Was wird geändert?

- `10-from-unprotected-to-secure.md` wird das kanonische Dokument für den Sicherheits-Lernpfad.
- Das doppelte Dokument `13-from-unprotected-to-secure.md` soll entfernt werden.
- Ein neues ausführliches Dokument beschreibt die aktuelle funktionierende V1, den Code und die erste Sicherungsmaßnahme mit `.env-logsink`.
- Ein neues Dokument plant schreibende Logging-Clients, weil bisher vor allem der Java-Viewer betrachtet wurde.
- `TODO.md`, `CHANGELOG.md`, `06-session-state.md`, `05-decision-log.md`, `README.md` und `database/mariadb/README.md` werden aktualisiert.

## Einspielen

Im Root des Repositories:

```bash
unzip -o LogSink_docs_tuesday_start_2026-04-28.zip -d .

# Das doppelte Sicherheitsdokument entfernen.
git rm docs/learning/13-from-unprotected-to-secure.md

git status
git diff --stat

git add CHANGELOG.md TODO.md database/mariadb/README.md docs/learning
git commit -m "Update LogSink learning docs for Tuesday planning"
git push origin main
```

## Danach prüfen

```bash
git status
git log --oneline --decorate -5
find docs/learning -maxdepth 1 -type f | sort
```

Erwartung: Es gibt nur noch `10-from-unprotected-to-secure.md`, nicht mehr zusätzlich `13-from-unprotected-to-secure.md`.

# LogSink Docs Cleanup 2026-04-27

Dieses Paket aktualisiert die Dokumentation nach dem Montagmorgen-Deployment-Test.

## Enthaltene Änderungen

- `docs/learning/README.md` neu formatiert und vollständig indexiert.
- `docs/learning/06-session-state.md` auf den aktuellen Stand gebracht.
- `docs/learning/03a-learning-plan-addendum-2026-04-27.md` bereinigt.
- `docs/learning/05-decision-log.md` um IONOS-/Konfigurationsentscheidungen ergänzt.
- `docs/learning/13-from-unprotected-to-secure.md` konsolidiert.
- `docs/learning/14-ionos-deployment-notes.md` aktualisiert.
- `docs/learning/15-java-client-configuration-plan.md` neu ergänzt.
- `docs/learning/16-code-commenting-plan.md` neu ergänzt.
- `docs/learning/99-open-questions.md` aktualisiert.
- `database/mariadb/README.md` ergänzt.
- `services/log-sink/README.md` aktualisiert.
- `CHANGELOG.md` ausführlicher für den 2026-04-27-Stand formuliert.
- `TODO.md` bereinigt und neu priorisiert.

## Einspielen

Im Root des Repositories:

```bash
unzip -o LogSink_docs_cleanup_2026-04-27.zip -d .
git status
git diff --stat
git add CHANGELOG.md TODO.md docs/learning database/mariadb/README.md services/log-sink/README.md
git commit -m "Update LogSink documentation after IONOS deployment test"
git push origin main
```

Dieses Paket enthält bewusst keine Codeänderungen.

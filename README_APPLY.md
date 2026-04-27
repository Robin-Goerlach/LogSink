# LogSink Dokumentations-Update 2026-04-27

Dieses Paket dokumentiert den Montagmorgen-Stand:

- Remote-V1-Service läuft bei IONOS.
- Java-Client kann Remote-Logs anzeigen.
- `.env` wurde aus dem öffentlich erreichbaren Service-Verzeichnis entfernt.
- externe `.env-logsink` wird unterstützt.
- SQL wurde in lokale DB-Erzeugung, Schema und Demo-Daten getrennt.
- Diagnose-Skript wurde nach `tools/diagnostics` verschoben.
- neue LS-Schritte für IONOS, Client-Konfiguration, Code-Kommentierung und den Weg von ungeschützt zu sicher wurden ergänzt.

## Vorher noch sauber aufräumen

Bei dir ist noch die gelöschte Datei `services/log-sink/php-diagnose.php` offen. Da das Diagnose-Skript nach `tools/diagnostics` verschoben wurde, sollte die Löschung sauber committed werden:

```bash
git add -u services/log-sink/php-diagnose.php
git commit -m "Remove diagnostic script from service directory"
git push origin main
```

## Einspielen

Im Root des Repositories:

```bash
unzip -o LogSink_2026-04-27_docs_update.zip -d .
git status
git add TODO.md CHANGELOG.md docs/learning
git commit -m "Document IONOS V1 deployment and next learning steps"
git push origin main
```

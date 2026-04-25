#!/usr/bin/env bash
set -euo pipefail

echo "Dieses Skript ersetzt den Arbeitsbaum durch die bereinigte Monorepo-Struktur."
echo "Es muss aus dem Root deines bestehenden Git-Repositories gestartet werden."
echo

if [ ! -d .git ]; then
  echo "Fehler: Kein .git-Verzeichnis gefunden. Bitte im Repository-Root starten."
  exit 1
fi

if [ "${1:-}" = "" ]; then
  echo "Verwendung:"
  echo "  ./scripts/cleanup-local-repo.sh /pfad/zum/entpackten/LogSink_rebuilt"
  exit 1
fi

SOURCE_DIR="$1"

if [ ! -d "$SOURCE_DIR" ]; then
  echo "Fehler: Source-Verzeichnis existiert nicht: $SOURCE_DIR"
  exit 1
fi

BACKUP="../LogSink-backup-$(date +%Y%m%dT%H%M%S).tar.gz"
echo "Erzeuge Backup ohne .git: $BACKUP"
tar --exclude='.git' -czf "$BACKUP" .

echo "Synchronisiere bereinigte Struktur..."
rsync -a --delete --exclude='.git' "$SOURCE_DIR"/ ./

echo "Fertig. Bitte prüfen:"
echo "  git status"
echo "  git diff --stat"

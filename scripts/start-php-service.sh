#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/../services/log-sink"

if [ ! -f .env ]; then
  echo "Hinweis: services/log-sink/.env fehlt."
  echo "Erzeuge .env aus .env.example. Bitte DB_PASSWORD prüfen."
  cp .env.example .env
fi

php -S 127.0.0.1:8080 public/index.php

#!/usr/bin/env bash
set -euo pipefail

# Sendet eine einfache Text-Logmeldung.
#
# Diese Variante zeigt, dass die aktuelle V0/V1 nicht nur JSON speichert,
# sondern den gesamten Request-Body unverändert übernimmt.

LOGSINK_URL="${LOGSINK_URL:-http://api.sasd.de/logsink/index.php}"
TIMESTAMP="$(date -Iseconds)"
MESSAGE="${TIMESTAMP} INFO curl-text-sender Hello from plain text curl sender"

echo "POST ${LOGSINK_URL}"
echo "${MESSAGE}"
echo

curl -i -X POST "${LOGSINK_URL}" \
  -H "Content-Type: text/plain; charset=utf-8" \
  -H "User-Agent: SASD-curl-text-sender/0.1" \
  --data-binary "${MESSAGE}"

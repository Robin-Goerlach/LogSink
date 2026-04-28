#!/usr/bin/env bash
set -euo pipefail

# Liest die letzten Logmeldungen vom aktuellen LogSink-Service.
#
# Nutzung:
#
#   ./examples/log-readers/curl/get-latest-logs.sh
#   ./examples/log-readers/curl/get-latest-logs.sh 10

LOGSINK_URL="${LOGSINK_URL:-http://api.sasd.de/logsink/index.php}"
LIMIT="${1:-5}"

echo "GET ${LOGSINK_URL}?limit=${LIMIT}"
echo

curl -i "${LOGSINK_URL}?limit=${LIMIT}"

#!/usr/bin/env bash
set -euo pipefail

# Sendet eine einfache JSON-Logmeldung an den aktuellen LogSink-Service.
#
# Konfiguration:
#
#   export LOGSINK_URL="http://api.sasd.de/logsink/index.php"
#
# oder lokal:
#
#   export LOGSINK_URL="http://127.0.0.1:8080/api/logs"
#
# Wenn LOGSINK_URL nicht gesetzt ist, wird die aktuelle IONOS-Test-URL genutzt.

LOGSINK_URL="${LOGSINK_URL:-http://api.sasd.de/logsink/index.php}"
TIMESTAMP="$(date -Iseconds)"

PAYLOAD="$(cat <<JSON
{
  "timestamp": "${TIMESTAMP}",
  "level": "INFO",
  "service": "curl-json-sender",
  "message": "Hello from curl JSON sender",
  "context": {
    "example": "post-json-log.sh",
    "project": "LogSink",
    "senderType": "curl"
  }
}
JSON
)"

echo "POST ${LOGSINK_URL}"
echo "${PAYLOAD}"
echo

curl -i -X POST "${LOGSINK_URL}" \
  -H "Content-Type: application/json; charset=utf-8" \
  -H "User-Agent: SASD-curl-json-sender/0.1" \
  --data-binary "${PAYLOAD}"

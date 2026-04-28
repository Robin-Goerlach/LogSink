#!/usr/bin/env bash
set -euo pipefail

# Sendet eine Beispiel-Fehlermeldung als JSON.
#
# Diese Meldung ist nützlich, um im Java-Viewer Filter, Sortierung und
# Detailanzeige mit einem ERROR-Ereignis zu prüfen.

LOGSINK_URL="${LOGSINK_URL:-http://api.sasd.de/logsink/index.php}"
TIMESTAMP="$(date -Iseconds)"

PAYLOAD="$(cat <<JSON
{
  "timestamp": "${TIMESTAMP}",
  "level": "ERROR",
  "service": "curl-error-sender",
  "message": "Simulated error from curl sender",
  "context": {
    "example": "post-error-log.sh",
    "exception": "DemoException",
    "file": "examples/log-senders/curl/post-error-log.sh",
    "line": 42,
    "hint": "This is a demo error, not a real application failure."
  }
}
JSON
)"

echo "POST ${LOGSINK_URL}"
echo "${PAYLOAD}"
echo

curl -i -X POST "${LOGSINK_URL}" \
  -H "Content-Type: application/json; charset=utf-8" \
  -H "User-Agent: SASD-curl-error-sender/0.1" \
  --data-binary "${PAYLOAD}"

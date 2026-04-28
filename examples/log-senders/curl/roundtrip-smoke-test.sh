#!/usr/bin/env bash
set -euo pipefail

# Kleiner Smoke-Test:
#
#   1. eindeutige Testmeldung senden
#   2. letzte Logmeldungen abrufen
#   3. prüfen, ob die Test-ID im Ergebnis vorkommt
#
# Das ist noch kein vollständiger automatisierter Test, aber ein nützlicher
# manueller Roundtrip:
#
#   Sender -> Service -> Datenbank -> GET-API

LOGSINK_URL="${LOGSINK_URL:-http://api.sasd.de/logsink/index.php}"
RUN_ID="logsink-smoke-$(date +%Y%m%dT%H%M%S)-$RANDOM"
TIMESTAMP="$(date -Iseconds)"

PAYLOAD="$(cat <<JSON
{
  "timestamp": "${TIMESTAMP}",
  "level": "INFO",
  "service": "curl-roundtrip-smoke-test",
  "message": "Roundtrip smoke test",
  "context": {
    "runId": "${RUN_ID}",
    "expectedFlow": "sender -> service -> database -> reader"
  }
}
JSON
)"

echo "POST ${LOGSINK_URL}"
echo "RUN_ID=${RUN_ID}"
echo

curl -sS -i -X POST "${LOGSINK_URL}" \
  -H "Content-Type: application/json; charset=utf-8" \
  -H "User-Agent: SASD-curl-roundtrip-smoke-test/0.1" \
  --data-binary "${PAYLOAD}"

echo
echo
echo "GET ${LOGSINK_URL}?limit=10"
echo

RESPONSE="$(curl -sS "${LOGSINK_URL}?limit=10")"

echo "${RESPONSE}"

if printf '%s' "${RESPONSE}" | grep -q "${RUN_ID}"; then
  echo
  echo "OK: Roundtrip message was found."
else
  echo
  echo "ERROR: Roundtrip message was not found in latest logs." >&2
  exit 1
fi

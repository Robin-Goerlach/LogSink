#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${1:-http://127.0.0.1:8080}"

echo "POST test log entry..."
curl -i -X POST "${BASE_URL}/api/logs" \
  -H "Content-Type: application/json; charset=utf-8" \
  --data-binary '{"level":"INFO","service":"smoke-test","message":"Smoke test log entry"}'

echo
echo "GET latest log entries..."
curl -i "${BASE_URL}/api/logs?limit=5"

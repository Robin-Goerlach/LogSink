#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/../clients/java-log-viewer"

mvn clean package
java -jar target/sasd-log-viewer-java-1.0.0.jar

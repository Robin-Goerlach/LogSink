# Learning Plan - von LogSink V1 zum sicheren Logging-Service

## Überblick

Der Lehrplan ist in Phasen gegliedert. Jede Phase besteht aus kleinen Schritten. Ein Schritt soll so klein sein, dass er verstanden, getestet und mit einem sinnvollen Commit abgeschlossen werden kann.

## Phasen

| Phase | Thema | Ergebnis |
|---|---|---|
| 0 | Orientierung | Projekt ist verstanden und dokumentiert |
| 1 | Arbeitsfähigkeit | Start, Tests, Maven, curl, `.env` sind sicher beherrscht |
| 2 | API-Grundlage | Routing, Request-ID und JSON-Antwortmodell |
| 3 | Composer und Tests | PHP-Projekt wird professioneller testbar |
| 4 | Datenbankbasis V1 | neues strukturiertes Schema neben Legacy |
| 5 | Strukturierter Ingest | Events validiert annehmen |
| 6 | Lesepfade | Events und Sources über Views lesen |
| 7 | Authentifizierung | Bearer-Tokens, Hashing, Pepper |
| 8 | Autorisierung | Principal-Typen und Scopes |
| 9 | Audit und Rate-Limiting | Betriebssicherheit und Nachvollziehbarkeit |
| 10 | Java-Client-Ausbau | Konfiguration, Tokens, Paging, Details, Export |
| 11 | Deployment | IONOS-/Hosting-Vorbereitung |
| 12 | Dokumentation | phpDocumentor, README, Betriebsdoku |

---

## Phase 0 - Orientierung und Projektzustand sichern

### LS-000: Repository-Status prüfen

**Ziel:** Sicherstellen, dass wir auf einem sauberen Stand arbeiten.

**Warum:** Jeder Lehrschritt soll reproduzierbar sein.

**Lerninhalt:** `git status`, Branches, Commit-Historie.

**Betroffene Dateien:** keine.

**Test:**

```bash
git status
git log --oneline --decorate -5
find services/log-sink -maxdepth 3 -type f | sort
find clients/java-log-viewer -type f | sort | head -50
```

**Commit:** keiner.

### LS-001: Learning-Dokumente einspielen

**Ziel:** Dieses Dokumentationspaket ins Repository übernehmen.

**Warum:** Damit der rote Faden über mehrere Tage erhalten bleibt.

**Betroffene Dateien:**

- `TODO.md`
- `docs/learning/*`

**Test:**

```bash
git status
git diff --stat
```

**Commit-Vorschlag:**

```text
Add LogSink learning plan documents
```

---

## Phase 1 - Aktuelle V1 sicher bedienen

### LS-010: PHP-Service lokal starten

**Ziel:** Den aktuellen PHP-Service zuverlässig starten.

**Warum:** Bevor wir umbauen, müssen wir wissen, wie der aktuelle Stand funktioniert.

**Lerninhalt:**

- `.env.example` nach `.env`
- PHP Development Server
- Unterschied Root-`index.php` und `public/index.php`
- Service-Root `services/log-sink`

**Befehle:**

```bash
cd services/log-sink
cp .env.example .env
php -S 127.0.0.1:8080 public/index.php
```

**Test:**

```bash
curl -i http://127.0.0.1:8080/api/logs?limit=5
```

**Commit:** keiner, sofern nur lokal gestartet.

### LS-011: MariaDB-Demo-Schema einspielen

**Ziel:** Die aktuelle Datenbankbasis nachvollziehbar herstellen.

**Warum:** Service und Client brauchen eine reale Datenbank.

**Lerninhalt:**

- SQL-Datei ausführen
- Datenbank `sasd_logging`
- Tabelle `log_entries`
- Trigger
- View

**Befehl:**

```bash
mysql -u root -p < database/mariadb/mariadb_10_11.sql
```

**Test:**

```bash
mysql -u root -p -e "USE sasd_logging; SELECT COUNT(*) FROM log_entries;"
```

**Commit:** keiner.

### LS-012: Aktuelle POST-/GET-Funktion mit curl testen

**Ziel:** Schreiben und Lesen der aktuellen V1 prüfen.

**Warum:** Das ist unsere Referenz vor dem Umbau.

**Test Schreiben:**

```bash
curl -i -X POST "http://127.0.0.1:8080/api/logs" \
  -H "Content-Type: application/json; charset=utf-8" \
  --data-binary '{"level":"INFO","service":"learning","message":"current V1 smoke test"}'
```

**Test Lesen:**

```bash
curl -i "http://127.0.0.1:8080/api/logs?limit=10"
```

**Commit-Vorschlag:**

Nur wenn Skripte oder Doku angepasst werden:

```text
Document current LogSink V1 smoke test
```

### LS-013: Java-Client als Maven-Projekt starten

**Ziel:** Den vorhandenen Java-Viewer bauen und starten.

**Warum:** Später müssen wir ihn an jede API-Änderung anpassen.

**Lerninhalt:**

- `pom.xml`
- Maven Lifecycle
- `mvn clean package`
- ausführbare JAR
- Swing-Client

**Befehle:**

```bash
cd clients/java-log-viewer
mvn clean package
java -jar target/sasd-log-viewer-java-1.0.0.jar
```

**Test:**

- Fenster öffnet sich.
- Service-URL steht auf `http://127.0.0.1:8080/api/logs`.
- Klick auf `Aktualisieren`.
- Logs erscheinen in der Tabelle.
- Spalten lassen sich sortieren.

**Typische Fehler:**

| Fehler | Ursache |
|---|---|
| `mvn: command not found` | Maven nicht installiert |
| Verbindung fehlgeschlagen | PHP-Service läuft nicht |
| JSON-Fehler | API-Response passt nicht zum Clientmodell |

**Commit:** keiner.

---

## Phase 2 - API-Grundlage modernisieren

### LS-020: Ziel-API-Vertrag V1.1 dokumentieren

**Ziel:** Vor Codeänderung festlegen, wohin die Schnittstelle geht.

**Warum:** Client und Server müssen denselben Vertrag kennen.

**Betroffene Dateien:**

- `contracts/http-api/logs-v1.md`
- optional `contracts/http-api/logs-v1.1.md`

**Inhalt:**

- alte V0/V1 Endpunkte
- neue `/api/v1/...` Endpunkte
- Übergangsstrategie
- Response-Modell
- Fehlerformat

**Test:**

Review des Markdown-Dokuments.

**Commit-Vorschlag:**

```text
Document target HTTP API v1.1 contract
```

### LS-021: Request-ID einführen

**Ziel:** Jede Antwort erhält eine `requestId`.

**Warum:** Fehler und Clientmeldungen können später korreliert werden.

**Service-Dateien:**

- `services/log-sink/src/App.php`
- ggf. neue Klasse `RequestContext.php`

**Client-Dateien:**

- zunächst keine Pflichtänderung

**Test:**

```bash
curl -i "http://127.0.0.1:8080/api/logs?limit=1"
```

Antwort enthält `requestId`.

**Commit-Vorschlag:**

```text
Add request id to service responses
```

### LS-022: Einheitliches JSON-Antwortmodell einführen

**Ziel:** Antworten schrittweise auf `ok/requestId/data` und `ok/error` umstellen.

**Warum:** Ziel-API und Client brauchen ein stabiles Antwortmodell.

**Service-Dateien:**

- neue Klasse `JsonResponse.php`
- `App.php`

**Client-Dateien:**

- `LogResponse.java`
- `LogServiceClient.java`

**Übergangsstrategie:**

Der Service kann kurzfristig altes und neues Modell unterstützen, oder der Client wird direkt angepasst.

**Test:**

```bash
curl -i "http://127.0.0.1:8080/api/logs?limit=5"
```

**Commit-Vorschlag:**

```text
Introduce standard JSON response envelope
```

### LS-023: Einfaches Routing einführen

**Ziel:** Nicht mehr nur HTTP-Methode auswerten, sondern Route.

**Warum:** Zielsystem braucht mehrere Endpunkte.

**Neue Routen zunächst:**

```text
GET /api/v1/health
GET /api/logs
POST /api/logs
```

**Service-Dateien:**

- `App.php`
- optional neue Klasse `Router.php`

**Test:**

```bash
curl -i "http://127.0.0.1:8080/api/v1/health"
curl -i "http://127.0.0.1:8080/api/logs?limit=5"
```

**Commit-Vorschlag:**

```text
Add basic route handling to PHP service
```

### LS-024: Front-Controller-Route-Parameter ergänzen

**Ziel:** `/index.php?route=/api/v1/health` unterstützen.

**Warum:** Zielsystem ist shared-hosting-freundlich und benötigt kein URL-Rewriting.

**Service-Dateien:**

- `App.php`
- ggf. `Request.php`

**Test:**

```bash
curl -i "http://127.0.0.1:8080/index.php?route=/api/v1/health"
```

**Commit-Vorschlag:**

```text
Support route query parameter for shared hosting
```

---

## Phase 3 - Composer, Autoloading und PHPUnit

### LS-030: Composer initialisieren

**Ziel:** PHP-Service professionell als Composer-Projekt vorbereiten.

**Warum:** PHPUnit, phpDocumentor und PSR-4-Autoloading werden später einfacher.

**Befehl:**

```bash
cd services/log-sink
composer init
```

**Empfohlene Werte:**

- package: `sasd/log-sink`
- description: `SASD LogSink PHP logging service`
- type: `project`
- license: `MIT`

**Betroffene Dateien:**

- `services/log-sink/composer.json`

**Commit-Vorschlag:**

```text
Initialize Composer project for PHP log sink
```

### LS-031: PSR-4-Autoloading einrichten

**Ziel:** Klassen nicht mehr nur über eigenen Autoloader laden.

**Warum:** Das ist Standard für PHP-Projekte und Voraussetzung für viele Tools.

**composer.json:**

```json
{
  "autoload": {
    "psr-4": {
      "Sasd\\LogSink\\": "src/"
    }
  }
}
```

**Befehl:**

```bash
composer dump-autoload
```

**Test:**

```bash
php -S 127.0.0.1:8080 public/index.php
```

**Commit-Vorschlag:**

```text
Add PSR-4 autoloading for PHP service
```

### LS-032: PHPUnit installieren

**Ziel:** erste automatisierte PHP-Tests ermöglichen.

**Befehl:**

```bash
cd services/log-sink
composer require --dev phpunit/phpunit
```

**Betroffene Dateien:**

- `composer.json`
- `composer.lock`
- `phpunit.xml`
- `tests/`

**Test:**

```bash
vendor/bin/phpunit --version
```

**Commit-Vorschlag:**

```text
Add PHPUnit test infrastructure
```

### LS-033: Erste Unit-Tests für Config

**Ziel:** eine einfache Klasse testbar machen.

**Warum:** Config ist klein und gut geeignet, Testgrundlagen zu lernen.

**Test-Datei:**

```text
services/log-sink/tests/ConfigTest.php
```

**Test:**

```bash
vendor/bin/phpunit
```

**Commit-Vorschlag:**

```text
Add unit tests for service configuration
```

---

## Phase 4 - Datenbankbasis für Zielmodell

### LS-040: Legacy-Tabelle einordnen

**Ziel:** `log_entries` als Legacy/V0 dokumentieren.

**Warum:** Nicht löschen, bevor neue Pfade stabil sind.

**Dateien:**

- `database/mariadb/README.md`
- ggf. neues `database/mariadb/legacy_v0.sql`

**Commit-Vorschlag:**

```text
Document legacy raw log storage
```

### LS-041: Migrationen-Struktur einführen

**Ziel:** SQL-Dateien in nachvollziehbare Schritte zerlegen.

**Vorschlag:**

```text
database/mariadb/
├── 001_legacy_raw_logs.sql
├── 010_core_schema.sql
├── 020_credentials.sql
├── 030_views.sql
├── 040_demo_seed.sql
└── README.md
```

**Commit-Vorschlag:**

```text
Organize MariaDB scripts as incremental schema files
```

### LS-042: Kerntabellen für strukturierte Events entwerfen

**Ziel:** Tabellen für `ingest_requests` und `log_events`.

**Warum:** Structured Logging braucht mehr als Rohtext.

**Test:**

```bash
mysql -u root -p < database/mariadb/010_core_schema.sql
```

**Commit-Vorschlag:**

```text
Add core schema for structured log events
```

### LS-043: Audit- und Source-Netzwerk-Tabellen ergänzen

**Ziel:** `access_audit` und `log_source_networks`.

**Warum:** Sicherheit braucht Nachvollziehbarkeit und optional IP-Allowlisting.

**Commit-Vorschlag:**

```text
Add audit and source network schema
```

### LS-044: Credentials und Scopes ergänzen

**Ziel:** Tabellen für Source- und Client-Credentials sowie Scopes.

**Warum:** Authentifizierung und Autorisierung brauchen Datenbasis.

**Commit-Vorschlag:**

```text
Add credential and scope schema
```

### LS-045: Read-Views ergänzen

**Ziel:** `api_v1_log_events` und `api_v1_log_sources`.

**Warum:** Clients sollen nicht direkt Basistabellen lesen.

**Commit-Vorschlag:**

```text
Add API read views for events and sources
```

---

## Phase 5 - Strukturierter Ingest

### LS-050: Ingest-Endpunkt ohne Auth einführen

**Ziel:** `/api/v1/ingest/events` akzeptiert strukturierte JSON-Events, noch ohne Sicherheit.

**Warum:** Erst Payload und Datenmodell verstehen, danach Sicherheit.

**Service-Dateien:**

- `App.php`
- neue `IngestController.php`
- neue `IngestEventValidator.php`
- `LogRepository.php` oder neues Repository

**Test:**

```bash
curl -i -X POST "http://127.0.0.1:8080/index.php?route=/api/v1/ingest/events" \
  -H "Content-Type: application/json" \
  --data '{"occurredAt":"2026-04-25T10:00:00Z","observedAt":"2026-04-25T10:00:01Z","severityNumber":17,"severityText":"ERROR","message":"Structured test event"}'
```

**Commit-Vorschlag:**

```text
Add structured ingest endpoint without authentication
```

### LS-051: JSON-Body-Regeln erzwingen

**Ziel:** Content-Type, JSON-Gültigkeit, Root-Objekt und Payload-Größe prüfen.

**Warum:** Viele Sicherheits- und Robustheitsprobleme entstehen vor der Fachlogik.

**Negative Tests:**

```bash
curl -i -X POST ".../index.php?route=/api/v1/ingest/events" --data 'not json'
curl -i -X POST ".../index.php?route=/api/v1/ingest/events" -H "Content-Type: text/plain" --data '{}'
```

**Commit-Vorschlag:**

```text
Validate JSON request bodies for ingest endpoint
```

### LS-052: Eventvalidierung ausbauen

**Ziel:** Pflichtfelder, Typen und Grenzen prüfen.

**Warum:** Der Service darf keine beliebigen Daten als Fachereignis speichern.

**Regeln:**

- `severityNumber` Integer 1..24
- `severityText` Pflicht
- `message` Pflicht
- Zeitfelder ISO-8601
- `attributes` Objekt/Array

**Commit-Vorschlag:**

```text
Add structured log event validation
```

### LS-053: Batch-Ingest unterstützen

**Ziel:** Einzel-Event und `{ "events": [...] }` akzeptieren.

**Warum:** Zielsystem erwähnt Einzel- und Batch-Grundidee.

**Commit-Vorschlag:**

```text
Support batch ingest payloads
```

---

## Phase 6 - Lesepfade

### LS-060: Eventliste über `/api/v1/events`

**Ziel:** Eventliste aus neuer View lesen.

**Warum:** Java-Client soll später nicht mehr aus Legacy-View lesen.

**Query-Parameter:**

- `page`
- `pageSize`
- `sourceKey`
- `severityText`
- `traceId`
- `correlationId`
- `from`
- `to`
- `sort`
- `direction`

**Commit-Vorschlag:**

```text
Add paginated event read endpoint
```

### LS-061: Einzel-Event über `/api/v1/events/{eventId}`

**Ziel:** Details zu einem Event lesen.

**Warum:** Client braucht Detailansicht und Related-Events.

**Commit-Vorschlag:**

```text
Add event detail endpoint
```

### LS-062: Quellenliste über `/api/v1/sources`

**Ziel:** tenantbezogene Sources lesen.

**Warum:** Client braucht Quellenübersicht und Filterhilfe.

**Commit-Vorschlag:**

```text
Add source list endpoint
```

---

## Phase 7 - Authentifizierung

### LS-070: Bearer-Header technisch prüfen

**Ziel:** geschützte Endpunkte verlangen `Authorization: Bearer ...`.

**Warum:** Erst syntaktische Prüfung, dann Datenbankprüfung.

**Test ohne Token:**

Erwartung: 401.

**Commit-Vorschlag:**

```text
Require bearer token for protected endpoints
```

### LS-071: Token-Hashing einführen

**Ziel:** Token nicht im Klartext speichern.

**Warum:** Tokens sind Zugangsdaten.

**Lerninhalt:**

- SHA-256
- Pepper
- Klartexttoken nur bei Provisionierung und Client

**Commit-Vorschlag:**

```text
Add hashed bearer token authentication
```

### LS-072: Source- und Client-Principals trennen

**Ziel:** Schreib- und Leseidentitäten unterscheiden.

**Warum:** Ein Lesetoken darf nicht schreiben; ein Sourcetoken darf nicht lesen.

**Commit-Vorschlag:**

```text
Separate source and client principals
```

---

## Phase 8 - Autorisierung

### LS-080: Scope-Prüfung einführen

**Ziel:** Endpunkte prüfen benötigte Scopes.

**Scopes:**

- `events.ingest`
- `events.read`
- `sources.read`

**Commit-Vorschlag:**

```text
Add scope based authorization
```

### LS-081: Java-Client mit Read-Token aus Konfiguration

**Ziel:** Lesender Client sendet Bearer-Token.

**Client-Dateien:**

- `client-settings.example.json`
- Settings-Klassen
- `LogServiceClient.java`

**Commit-Vorschlag:**

```text
Add read bearer token support to Java viewer
```

### LS-082: Separaten Ingest-Token im Client vorbereiten

**Ziel:** Test-Log-Versand später sauber absichern.

**Warum:** Client-Dokumente fordern getrennte Lese- und Ingest-Berechtigung.

**Commit-Vorschlag:**

```text
Prepare separate ingest token configuration
```

---

## Phase 9 - Audit, IP-Allowlisting und Rate-Limit

### LS-090: Access-Audit schreiben

**Ziel:** geschützte Zugriffe nachvollziehbar speichern.

**Commit-Vorschlag:**

```text
Add access audit logging
```

### LS-091: IP-Allowlisting für Sources

**Ziel:** Source-Zugriffe optional auf erlaubte Netze einschränken.

**Commit-Vorschlag:**

```text
Add source network allowlist checks
```

### LS-092: File-basiertes Rate-Limiting

**Ziel:** Missbrauch und Teststürme begrenzen.

**Commit-Vorschlag:**

```text
Add file based rate limiting
```

---

## Phase 10 - Java-Client fachlich ausbauen

### LS-100: Client-Konfiguration einführen

**Ziel:** URL, Route-Parameter, API-Version, Timeout, Page-Size und Tokens aus Datei laden.

**Commit-Vorschlag:**

```text
Add Java viewer settings file
```

### LS-101: Healthcheck im Client

**Ziel:** Verbindung bewusst testen.

**Commit-Vorschlag:**

```text
Add health check action to Java viewer
```

### LS-102: Paging und Serverfilter

**Ziel:** Eventliste nicht nur letzte 100 Datensätze lesen.

**Commit-Vorschlag:**

```text
Add paging and server filters to Java viewer
```

### LS-103: Detailansicht über API

**Ziel:** Detaildaten per `/api/v1/events/{eventId}` laden.

**Commit-Vorschlag:**

```text
Load event details from API
```

### LS-104: Related Events

**Ziel:** Folgeabfragen über `traceId` oder `correlationId`.

**Commit-Vorschlag:**

```text
Add related events lookup
```

### LS-105: Sources-Ansicht

**Ziel:** Quellenliste anzeigen.

**Commit-Vorschlag:**

```text
Add source overview to Java viewer
```

### LS-106: Test-Log-Versand

**Ziel:** kontrollierter Versand technischer Testevents mit Source-Token.

**Commit-Vorschlag:**

```text
Add controlled test log sender
```

### LS-107: CSV-/JSON-Export

**Ziel:** sichtbare Datenmenge exportieren.

**Commit-Vorschlag:**

```text
Add CSV and JSON export to Java viewer
```

### LS-108: Lokales Diagnose-Logging

**Ziel:** Clientfehler nachvollziehbar machen, ohne Tokens zu leaken.

**Commit-Vorschlag:**

```text
Add sanitized diagnostic logging to Java viewer
```

---

## Phase 11 - Deployment und IONOS-Vorbereitung

### LS-110: Deployment-Checkliste erstellen

**Ziel:** lokale und produktionsnahe Installation dokumentieren.

**Commit-Vorschlag:**

```text
Add deployment checklist
```

### LS-111: IONOS-Variante klären

**Ziel:** Shared Hosting, VPS oder Managed Server unterscheiden.

**Warum:** Die Schritte unterscheiden sich stark.

**Commit-Vorschlag:**

```text
Document IONOS deployment options
```

### LS-112: Produktionsnahe `.env.example`

**Ziel:** Konfiguration ohne Secrets dokumentieren.

**Commit-Vorschlag:**

```text
Update service environment example for secure deployment
```

---

## Phase 12 - Dokumentation und Abschluss

### LS-120: phpDocumentor vorbereiten

**Ziel:** Code-Dokumentation erzeugen können.

**Befehl später:**

```bash
composer require --dev phpdocumentor/phpdocumentor
```

**Commit-Vorschlag:**

```text
Add phpDocumentor configuration
```

### LS-121: README-Dateien aktualisieren

**Ziel:** Start, Tests und Konfiguration stimmen mit neuer Struktur überein.

**Commit-Vorschlag:**

```text
Update service and client documentation
```

### LS-122: Abschlussreview

**Ziel:** Projektzustand prüfen.

**Checkliste:**

- Service startet lokal.
- Datenbankskripte laufen.
- curl-Smoke-Tests laufen.
- PHPUnit läuft.
- Java-Client startet.
- Java-Client kann lesen.
- README passt.
- TODO aktualisiert.
- Session-State aktualisiert.

**Commit-Vorschlag:**

```text
Finalize LogSink secure learning milestone
```

# Implementation Roadmap

## Leitprinzip

Jeder technische Schritt soll Service, Datenbank, Client und Dokumentation im Blick behalten.

Nicht jeder Schritt ändert alle Bereiche, aber jeder Schritt muss bewusst beantworten:

```text
Was bedeutet das für die Datenbank?
Was bedeutet das für den PHP-Service?
Was bedeutet das für den Java-Client?
Was bedeutet das für Tests?
Was bedeutet das für die Dokumentation?
```

## Roadmap als Tabelle

| Milestone | Datenbank | PHP-Service | Java-Client | Tests | Dokumentation |
|---|---|---|---|---|---|
| M0 Aktuelle V1 sichern | bestehendes Demo-Schema | aktueller GET/POST | aktueller Viewer | curl + UI | Session-State |
| M1 API-Basis | keine | Request-ID, JsonResponse, Router | Response-Modell anpassen | curl | API-Vertrag |
| M2 Tooling | keine | Composer, PSR-4 | Maven bleibt | PHPUnit startet | Beginner Guide |
| M3 Structured Schema | neue Tabellen/Views | Repositories vorbereiten | noch Legacy-kompatibel | SQL-Checks | DB-README |
| M4 Structured Ingest | `ingest_requests`, `log_events` | `/api/v1/ingest/events` | Testdialog später | curl Ingest | API-Vertrag |
| M5 Read API | Views | `/events`, `/events/{id}`, `/sources` | Liste/Details anpassen | curl Read | Client README |
| M6 Auth | Credentials/Scopes | Bearer, Hash, Pepper | Read-Token | Auth-Tests | Security-Doku |
| M7 Authorization | Scope-Zuordnung | `events.*`, `sources.*` | getrennte Tokens | 401/403 Tests | Troubleshooting |
| M8 Audit/Rate | `access_audit`, networks | Audit, Rate-Limit, IP-Allowlist | Fehlerdialoge | Negativtests | Betrieb |
| M9 Client V1+ | keine | stabile API | Paging, Filter, Export, Diagnostics | UI-Checks | Client-Doku |
| M10 Deployment | DB-Rechte | `.env`, Pfade | Konfigurationsprofile | Minimalnachweis | IONOS-Guide |

## Empfohlene Commit-Gruppierung

Ein Commit sollte einen nachvollziehbaren Zweck haben.

Gut:

```text
Add request id to service responses
Add basic route query parameter handling
Add PHPUnit infrastructure
Add structured ingest event validation
Add read bearer token support to Java viewer
```

Schlecht:

```text
Update stuff
Big refactoring
Security
```

## Übergangsstrategie

Wir vermeiden einen harten Bruch.

1. Alte `/api/logs`-Funktion kann anfangs erhalten bleiben.
2. Neue `/api/v1/...`-Endpunkte werden daneben aufgebaut.
3. Java-Client kann kurzfristig zwischen Legacy und API v1 wechseln.
4. Sobald API v1 stabil ist, wird Legacy als deprecated dokumentiert.
5. Erst später wird entschieden, ob Legacy entfernt wird.

## Branch-Strategie

Für größere Milestones:

```bash
git switch -c feature/api-response-envelope
```

Für kleine Doku-/Testschritte direkt auf `main` nur dann, wenn sicher und überschaubar.

Empfehlung: Auch kleine Code-Schritte auf Feature-Branch, dann Merge.

## Definition of Done pro Schritt

Ein Schritt ist fertig, wenn:

- Code kompiliert bzw. PHP-Service startet.
- Datenbankänderung reproduzierbar ist.
- curl-Test oder PHPUnit-Test existiert.
- Java-Client-Anpassung geprüft ist, falls betroffen.
- README/API-Doku aktualisiert ist.
- `git status` sauber ist.
- Commit-Message aussagekräftig ist.

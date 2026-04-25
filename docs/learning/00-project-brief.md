# SASD LogSink - Project Brief

## Ausgangspunkt

SASD LogSink besitzt aktuell eine bewusst einfache V1:

- PHP-Service unter `services/log-sink`
- Java-Swing-Viewer unter `clients/java-log-viewer`
- MariaDB-Skript unter `database/mariadb/mariadb_10_11.sql`
- HTTP-Vertrag unter `contracts/http-api/logs-v1.md`
- Schreiben per `POST`
- Lesen per `GET`
- keine Authentifizierung
- keine Autorisierung
- keine strukturierte Eventvalidierung
- Speicherung des Request-Bodys als Rohdaten

Diese V1 war absichtlich ungeschützt. Sie beweist zuerst den Grundfluss:

```text
Client -> HTTP -> PHP-Service -> MariaDB -> Java-Viewer
```

## Ziel

LogSink soll Schritt für Schritt zu einem sicheren, getesteten und dokumentierten Logging-Service weiterentwickelt werden.

Das Zielbild orientiert sich an den bereitgestellten Mustralla/MustelaLogAPI-Dokumenten:

- PHP-basierte HTTP/JSON-Middleware
- MariaDB als kontrollierte Datenhaltung
- strukturierte Logevents statt nur Rohdaten
- Bearer-Token-Authentifizierung
- getrennte Principal-Typen für `source` und `client`
- Scope-basierte Autorisierung
- tenantbezogene Lesepfade
- IP-Allowlisting für Quellen
- file-basiertes Rate-Limiting
- Audit-Logging
- standardisierte JSON-Antworten
- Java-Client als Bedien- und Präsentationsschicht
- spätere Vorbereitung für WPF-Client oder weitere Clients

## Didaktisches Ziel

Dieses Projekt ist ein Lehrprojekt.

Geschwindigkeit ist zweitrangig. Wichtig ist:

- kleine, atomare Schritte,
- Service, Datenbank und Client gemeinsam denken,
- jeden Schritt testen,
- Code erklären,
- Git-Commits bewusst setzen,
- Anfängerfragen ausdrücklich behandeln.

## Grundregel für jeden Schritt

Jeder Umsetzungsschritt enthält:

1. Ziel
2. Warum
3. Lerninhalt
4. betroffene Dateien
5. Datenbankänderungen
6. PHP-Service-Änderungen
7. Java-Client-Änderungen
8. Dokumentationsänderungen
9. Test mit curl, PHPUnit, Maven oder UI
10. erwartetes Ergebnis
11. typische Fehler
12. Commit-Vorschlag

## Nicht-Ziele für die nächsten Schritte

Nicht alles sofort bauen.

Zunächst nicht im Fokus:

- vollständiges SIEM
- Admin-Weboberfläche
- komplexes Benutzerverwaltungssystem
- Queue/Worker-Architektur
- Docker-Pflicht
- Kubernetes
- Volltextsuche
- perfekte Produktionshärtung in einem Schritt

## Entwicklungsprinzip

Immer zuerst stabilisieren, dann erweitern:

```text
Verstehen -> Vertrag dokumentieren -> kleine Änderung -> Test -> Commit -> nächster Schritt
```

## Repository-Kontext

Aktueller Zielaufbau:

```text
LogSink/
├── clients/
│   └── java-log-viewer/
├── contracts/
│   └── http-api/
├── database/
│   └── mariadb/
├── docs/
│   └── learning/
├── scripts/
└── services/
    └── log-sink/
```

## Leitgedanke

Der spätere sichere Service entsteht nicht durch einen großen Umbau, sondern durch eine nachvollziehbare Abfolge kleiner Verbesserungen.

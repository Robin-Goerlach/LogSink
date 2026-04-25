# Repository-Struktur

Dieses Repository ist als kleines Monorepo aufgebaut.

## Grundidee

```text
services/   Serverdienste und Service-Implementierungen
clients/    Benutzer- oder Systemclients
database/   Datenbankschemata, Demo-Daten und spätere Migrationen
contracts/  Schnittstellenverträge zwischen Services und Clients
docs/       technische Dokumentation
scripts/    lokale Hilfsskripte
```

## Services

Der aktuelle Service liegt unter:

```text
services/log-sink
```

Dieser Ordner ist der Root des PHP-Service. Deshalb besitzt er eine eigene `.env.example`, eine eigene `index.php`, eine eigene `public/index.php` und einen eigenen `src`-Ordner.

Spätere Service-Implementierungen könnten so ergänzt werden:

```text
services/
├── log-sink/             # PHP V1
├── dotnet-log-sink/      # mögliche spätere .NET-Implementierung
└── java-log-sink/        # mögliche spätere Java-Implementierung
```

## Clients

Der aktuelle Client liegt unter:

```text
clients/java-log-viewer
```

Spätere Clients könnten so ergänzt werden:

```text
clients/
├── java-log-viewer/
├── wpf-log-viewer/
├── web-log-viewer/
└── cli-log-client/
```

## API-Verträge

Die API wird nicht nur im Code beschrieben, sondern zusätzlich in `contracts/http-api`.

Dadurch können mehrere Clients und mehrere Service-Implementierungen dieselbe Schnittstelle verwenden.

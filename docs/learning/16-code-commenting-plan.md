# Code Commenting Plan

## Ziel

Der Code soll für Lernzwecke stärker kommentiert werden, ohne mit unnötigen Kommentaren überladen zu werden.

## Grundregel

Kommentiert wird nicht jede einzelne Zeile. Kommentiert werden:

- Verantwortung einer Klasse,
- wichtige Entwurfsentscheidungen,
- nicht offensichtliche technische Details,
- typische Stolperfallen,
- Sicherheitsrelevanz.

## PHP-Service

| Datei | Was erklärt werden soll |
|---|---|
| `Bootstrap.php` | Autoloader, frühe Konfiguration, `.env-logsink`, IONOS-/Shared-Hosting-Pfade |
| `Config.php` | `parse_ini_file`, Getter für string/int/bool, Defaults |
| `Database.php` | PDO, DSN, Exceptions, `utf8mb4` |
| `LogRepository.php` | SQL-Schicht, Prepared Statements, Insert/Read-Trennung |
| `App.php` | V1-Flow, `php://input`, JSON-Antworten, Fehlerbehandlung |
| `ServiceLogger.php` | Technik-Log vs. fachliche Logs, Schreibrechte |

## Java-Client

| Datei | Was erklärt werden soll |
|---|---|
| `Main.java` | Einstiegspunkt, Swing Event Dispatch Thread, Look and Feel |
| `LogServiceClient.java` | `HttpClient`, GET, URL-Aufbau, Timeout, Jackson |
| `LogEntry.java` | Java Record, JSON-Property-Mapping, Vorschau, Base64-Fallback |
| `LogResponse.java` | aktuelles V1-Response-Modell |
| `LogTableModel.java` | `JTable`, Spaltentypen, Sortierung, Mapping |
| `LogViewerFrame.java` | Fensteraufbau, Button, Filter, `SwingWorker`, Detaildialog |

## Qualitätsregel

Wenn ein Kommentar nur den Code wiederholt, ist er überflüssig.

Schlecht:

```java
// Setzt die URL
serviceUrlField.setText(url);
```

Gut:

```java
/*
 * Der HTTP-Abruf läuft in einem SwingWorker, damit die Oberfläche während
 * langsamer Netzwerkzugriffe nicht einfriert.
 */
```

## Commit-Vorschlag

```text
Add explanatory comments to service and Java viewer
```

package de.sasd.logsink.viewer.model;

import java.util.List;

/**
 * Antwortobjekt des aktuellen PHP-Service.
 *
 * Die V1-Antwort beim Lesen sieht ungefähr so aus:
 *
 * {
 *   "status": "ok",
 *   "items": [...]
 * }
 *
 * Dieses Record bildet genau dieses aktuelle Format ab.
 *
 * Später wird der Service wahrscheinlich ein neues Antwortmodell bekommen:
 *
 * {
 *   "ok": true,
 *   "requestId": "...",
 *   "data": {...}
 * }
 *
 * Dann muss auch dieses Modell angepasst oder durch ein neues Modell ersetzt
 * werden.
 */
public record LogResponse(
    String status,
    List<LogEntry> items
) {
}

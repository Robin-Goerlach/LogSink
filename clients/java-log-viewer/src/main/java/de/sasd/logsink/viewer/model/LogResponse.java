package de.sasd.logsink.viewer.model;

import java.util.List;

/**
 * Antwortobjekt des PHP-Service.
 */
public record LogResponse(
        String status,
        List<LogEntry> items
) {
}

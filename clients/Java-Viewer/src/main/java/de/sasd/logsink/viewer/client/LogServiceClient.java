package de.sasd.logsink.viewer.client;

import com.fasterxml.jackson.databind.DeserializationFeature;
import com.fasterxml.jackson.databind.ObjectMapper;
import de.sasd.logsink.viewer.model.LogEntry;
import de.sasd.logsink.viewer.model.LogResponse;

import java.io.IOException;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.time.Duration;
import java.util.List;

/**
 * Kleiner HTTP-Client für den PHP-Service SASD LogSink.
 *
 * Der Client erwartet:
 *   GET /api/logs?limit=100
 *
 * Die JSON-Antwort wird mit Jackson in Java-Objekte umgewandelt.
 */
public final class LogServiceClient {
    private final HttpClient httpClient;
    private final ObjectMapper objectMapper;

    public LogServiceClient() {
        this.httpClient = HttpClient.newBuilder()
                .connectTimeout(Duration.ofSeconds(5))
                .build();

        this.objectMapper = new ObjectMapper()
                .configure(DeserializationFeature.FAIL_ON_UNKNOWN_PROPERTIES, false);
    }

    public List<LogEntry> fetchLatestLogs(String baseUrl, int limit) throws IOException, InterruptedException {
        URI uri = buildUri(baseUrl, limit);

        HttpRequest request = HttpRequest.newBuilder()
                .uri(uri)
                .timeout(Duration.ofSeconds(15))
                .header("Accept", "application/json")
                .GET()
                .build();

        HttpResponse<String> response = httpClient.send(
                request,
                HttpResponse.BodyHandlers.ofString()
        );

        int statusCode = response.statusCode();

        if (statusCode < 200 || statusCode >= 300) {
            throw new IOException("HTTP " + statusCode + ": " + response.body());
        }

        LogResponse logResponse = objectMapper.readValue(response.body(), LogResponse.class);

        if (logResponse.items() == null) {
            return List.of();
        }

        return logResponse.items();
    }

    /**
     * Baut die URL für den PHP-Service.
     *
     * Einfach gehalten:
     * - wenn noch kein Query-String existiert: ?limit=...
     * - wenn bereits ein Query-String existiert: &limit=...
     */
    private URI buildUri(String baseUrl, int limit) {
        String trimmedUrl = baseUrl == null ? "" : baseUrl.trim();

        if (trimmedUrl.isEmpty()) {
            trimmedUrl = "http://127.0.0.1:8080/api/logs";
        }

        int safeLimit = Math.max(1, Math.min(1000, limit));
        String separator = trimmedUrl.contains("?") ? "&" : "?";

        return URI.create(trimmedUrl + separator + "limit=" + safeLimit);
    }
}

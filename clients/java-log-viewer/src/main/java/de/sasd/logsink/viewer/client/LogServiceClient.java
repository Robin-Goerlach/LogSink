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
 * Aufgabe:
 * --------
 * Diese Klasse kennt HTTP. Sie ruft den PHP-Service auf und wandelt die
 * JSON-Antwort in Java-Objekte um.
 *
 * Sie kennt aber keine Swing-Oberfläche. Dadurch bleibt die UI von der
 * Netzwerklogik getrennt.
 *
 * Aktueller V1-Stand:
 * -------------------
 * Der Client erwartet eine URL, die Logmeldungen lesen kann.
 *
 * Lokal:
 *
 *   http://127.0.0.1:8080/api/logs
 *
 * IONOS ohne Rewrite:
 *
 *   http://api.sasd.de/logsink/index.php
 *
 * fetchLatestLogs() hängt daran nur noch ?limit=... bzw. &limit=... an.
 */
public final class LogServiceClient {

    private final HttpClient httpClient;
    private final ObjectMapper objectMapper;

    public LogServiceClient() {
        /*
         * Java HttpClient ist seit Java 11 Teil der Standardbibliothek.
         *
         * connectTimeout begrenzt nur den Verbindungsaufbau. Der eigentliche
         * Request bekommt weiter unten zusätzlich ein Timeout.
         */
        this.httpClient = HttpClient.newBuilder()
            .connectTimeout(Duration.ofSeconds(5))
            .build();

        /*
         * Jackson wandelt JSON in Java Records/Klassen um.
         *
         * FAIL_ON_UNKNOWN_PROPERTIES=false ist bewusst gesetzt, damit der Client
         * nicht sofort bricht, wenn der Server später zusätzliche Felder liefert.
         */
        this.objectMapper = new ObjectMapper()
            .configure(DeserializationFeature.FAIL_ON_UNKNOWN_PROPERTIES, false);
    }

    /**
     * Ruft die letzten Logmeldungen vom Service ab.
     *
     * @param baseUrl Lese-URL des Services
     * @param limit gewünschte Anzahl an Logmeldungen
     * @return Liste der Logeinträge
     */
    public List<LogEntry> fetchLatestLogs(String baseUrl, int limit) throws IOException, InterruptedException {
        URI uri = buildUri(baseUrl, limit);

        HttpRequest request = HttpRequest.newBuilder()
            .uri(uri)
            .timeout(Duration.ofSeconds(15))
            .header("Accept", "application/json")
            .GET()
            .build();

        /*
         * Dieser Aufruf blockiert den aktuellen Thread.
         *
         * Deshalb wird fetchLatestLogs() aus LogViewerFrame nicht direkt auf dem
         * Swing-UI-Thread aufgerufen, sondern innerhalb eines SwingWorker.
         */
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
     *
     * - wenn noch kein Query-String existiert: ?limit=...
     * - wenn bereits ein Query-String existiert: &limit=...
     *
     * Später wird diese Methode vermutlich ersetzt oder erweitert, wenn der
     * Service auf route=/api/v1/events und echte Paging-Parameter umgestellt
     * wird.
     */
    private URI buildUri(String baseUrl, int limit) {
        String trimmedUrl = baseUrl == null ? "" : baseUrl.trim();

        if (trimmedUrl.isEmpty()) {
            trimmedUrl = "http://127.0.0.1:8080/api/logs";
        }

        /*
         * Der Client begrenzt limit bereits hier.
         * Das Repository begrenzt serverseitig ebenfalls. Clientseitige Prüfung
         * ist Komfort, serverseitige Prüfung ist Sicherheit/Stabilität.
         */
        int safeLimit = Math.max(1, Math.min(1000, limit));

        String separator = trimmedUrl.contains("?") ? "&" : "?";

        return URI.create(trimmedUrl + separator + "limit=" + safeLimit);
    }
}

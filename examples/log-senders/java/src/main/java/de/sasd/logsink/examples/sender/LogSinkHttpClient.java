package de.sasd.logsink.examples.sender;

import java.io.IOException;
import java.net.URI;
import java.net.http.HttpClient;
import java.net.http.HttpRequest;
import java.net.http.HttpResponse;
import java.time.Duration;

/**
 * Kleiner Java-HTTP-Client für den aktuellen LogSink-V0/V1-Service.
 *
 * Aufgabe:
 * --------
 * Diese Klasse kapselt die HTTP-Details:
 *
 * - POST mit Body senden,
 * - GET mit limit-Parameter senden,
 * - HTTP-Status und Body zurückgeben.
 *
 * Sie ist bewusst klein und nutzt nur Java-Standardbibliothek.
 */
public final class LogSinkHttpClient {

    public static final String DEFAULT_LOGSINK_URL = "http://api.sasd.de/logsink/index.php";

    private final String baseUrl;
    private final HttpClient httpClient;
    private final Duration requestTimeout;

    public LogSinkHttpClient(String baseUrl) {
        this(baseUrl, Duration.ofSeconds(15));
    }

    public LogSinkHttpClient(String baseUrl, Duration requestTimeout) {
        this.baseUrl = normalizeBaseUrl(baseUrl);
        this.requestTimeout = requestTimeout == null ? Duration.ofSeconds(15) : requestTimeout;
        this.httpClient = HttpClient.newBuilder()
            .connectTimeout(this.requestTimeout)
            .build();
    }

    /**
     * Erstellt einen Client aus der Umgebungsvariable LOGSINK_URL.
     *
     * Wenn die Variable nicht gesetzt ist, wird der aktuelle IONOS-Testbetrieb
     * als Default verwendet.
     */
    public static LogSinkHttpClient fromEnvironment() {
        String value = System.getenv("LOGSINK_URL");

        if (value == null || value.isBlank()) {
            value = DEFAULT_LOGSINK_URL;
        }

        return new LogSinkHttpClient(value.trim());
    }

    public String baseUrl() {
        return baseUrl;
    }

    /**
     * Sendet eine Logmeldung per POST an LogSink.
     */
    public HttpResult post(String payload, String contentType, String userAgent)
        throws IOException, InterruptedException {

        HttpRequest request = HttpRequest.newBuilder()
            .uri(URI.create(baseUrl))
            .timeout(requestTimeout)
            .header("Content-Type", contentType)
            .header("Accept", "application/json")
            .header("User-Agent", userAgent)
            .POST(HttpRequest.BodyPublishers.ofString(payload))
            .build();

        HttpResponse<String> response = httpClient.send(
            request,
            HttpResponse.BodyHandlers.ofString()
        );

        return new HttpResult(response.statusCode(), response.body());
    }

    /**
     * Liest die letzten Logmeldungen per GET.
     */
    public HttpResult getLatest(int limit) throws IOException, InterruptedException {
        int safeLimit = Math.max(1, Math.min(1000, limit));
        String separator = baseUrl.contains("?") ? "&" : "?";
        String url = baseUrl + separator + "limit=" + safeLimit;

        HttpRequest request = HttpRequest.newBuilder()
            .uri(URI.create(url))
            .timeout(requestTimeout)
            .header("Accept", "application/json")
            .header("User-Agent", "SASD-java-log-sender-reader/0.1")
            .GET()
            .build();

        HttpResponse<String> response = httpClient.send(
            request,
            HttpResponse.BodyHandlers.ofString()
        );

        return new HttpResult(response.statusCode(), response.body());
    }

    /**
     * Gibt eine Response einfach lesbar auf der Konsole aus.
     */
    public static void printResponse(HttpResult result) {
        System.out.println("HTTP " + result.statusCode());
        System.out.println();
        System.out.println(result.body());
    }

    private static String normalizeBaseUrl(String value) {
        if (value == null || value.isBlank()) {
            return DEFAULT_LOGSINK_URL;
        }

        return value.trim();
    }
}

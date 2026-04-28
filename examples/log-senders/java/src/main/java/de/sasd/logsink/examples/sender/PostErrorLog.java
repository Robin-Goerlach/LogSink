package de.sasd.logsink.examples.sender;

import java.time.OffsetDateTime;

/**
 * Sendet eine Beispiel-Fehlermeldung als JSON.
 */
public final class PostErrorLog {

    private PostErrorLog() {
    }

    public static void main(String[] args) throws Exception {
        LogSinkHttpClient client = LogSinkHttpClient.fromEnvironment();

        String payload = """
            {
              "timestamp": "%s",
              "level": "ERROR",
              "service": "java-error-sender",
              "message": "Simulated error from Java sender",
              "context": {
                "example": "PostErrorLog.java",
                "exception": "DemoException",
                "hint": "This is a demo error, not a real application failure."
              }
            }
            """.formatted(OffsetDateTime.now());

        System.out.println("POST " + client.baseUrl());
        System.out.println(payload);

        HttpResult result = client.post(
            payload,
            "application/json; charset=utf-8",
            "SASD-java-error-sender/0.1"
        );

        LogSinkHttpClient.printResponse(result);

        if (!result.isSuccessful()) {
            System.exit(1);
        }
    }
}

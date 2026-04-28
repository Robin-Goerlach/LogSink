package de.sasd.logsink.examples.sender;

import java.time.OffsetDateTime;

/**
 * Sendet eine einfache JSON-Logmeldung an LogSink.
 */
public final class PostJsonLog {

    private PostJsonLog() {
    }

    public static void main(String[] args) throws Exception {
        LogSinkHttpClient client = LogSinkHttpClient.fromEnvironment();

        String payload = """
            {
              "timestamp": "%s",
              "level": "INFO",
              "service": "java-json-sender",
              "message": "Hello from Java JSON sender",
              "context": {
                "example": "PostJsonLog.java",
                "project": "LogSink",
                "senderType": "java"
              }
            }
            """.formatted(OffsetDateTime.now());

        System.out.println("POST " + client.baseUrl());
        System.out.println(payload);

        HttpResult result = client.post(
            payload,
            "application/json; charset=utf-8",
            "SASD-java-json-sender/0.1"
        );

        LogSinkHttpClient.printResponse(result);

        if (!result.isSuccessful()) {
            System.exit(1);
        }
    }
}

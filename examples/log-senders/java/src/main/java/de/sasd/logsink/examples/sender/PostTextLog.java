package de.sasd.logsink.examples.sender;

import java.time.OffsetDateTime;

/**
 * Sendet eine einfache Textmeldung.
 *
 * Die aktuelle V0/V1 speichert den Body unverändert. Deshalb ist text/plain als
 * Testfall weiterhin sinnvoll.
 */
public final class PostTextLog {

    private PostTextLog() {
    }

    public static void main(String[] args) throws Exception {
        LogSinkHttpClient client = LogSinkHttpClient.fromEnvironment();

        String payload = OffsetDateTime.now()
            + " INFO java-text-sender Hello from plain text Java sender";

        System.out.println("POST " + client.baseUrl());
        System.out.println(payload);
        System.out.println();

        HttpResult result = client.post(
            payload,
            "text/plain; charset=utf-8",
            "SASD-java-text-sender/0.1"
        );

        LogSinkHttpClient.printResponse(result);

        if (!result.isSuccessful()) {
            System.exit(1);
        }
    }
}

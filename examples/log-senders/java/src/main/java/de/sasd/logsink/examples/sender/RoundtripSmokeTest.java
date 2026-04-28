package de.sasd.logsink.examples.sender;

import java.time.OffsetDateTime;
import java.time.format.DateTimeFormatter;
import java.util.concurrent.ThreadLocalRandom;

/**
 * Roundtrip-Smoke-Test für den Java-Sender.
 *
 * Ablauf:
 *
 *   1. eindeutige Meldung erzeugen,
 *   2. per POST an LogSink senden,
 *   3. letzte Meldungen per GET lesen,
 *   4. prüfen, ob die eindeutige runId wieder auftaucht.
 */
public final class RoundtripSmokeTest {

    private RoundtripSmokeTest() {
    }

    public static void main(String[] args) throws Exception {
        LogSinkHttpClient client = LogSinkHttpClient.fromEnvironment();

        String runId = "logsink-java-smoke-"
            + DateTimeFormatter.ofPattern("yyyyMMdd'T'HHmmss").format(OffsetDateTime.now())
            + "-"
            + ThreadLocalRandom.current().nextInt(1000, 10_000);

        String payload = """
            {
              "timestamp": "%s",
              "level": "INFO",
              "service": "java-roundtrip-smoke-test",
              "message": "Roundtrip smoke test from Java sender",
              "context": {
                "runId": "%s",
                "expectedFlow": "java sender -> service -> database -> reader"
              }
            }
            """.formatted(OffsetDateTime.now(), runId);

        System.out.println("POST " + client.baseUrl());
        System.out.println("RUN_ID=" + runId);
        System.out.println();

        HttpResult postResult = client.post(
            payload,
            "application/json; charset=utf-8",
            "SASD-java-roundtrip-smoke-test/0.1"
        );

        LogSinkHttpClient.printResponse(postResult);

        if (!postResult.isSuccessful()) {
            System.err.println("ERROR: POST failed.");
            System.exit(1);
        }

        System.out.println();
        System.out.println("GET " + client.baseUrl() + "?limit=10");
        System.out.println();

        HttpResult getResult = client.getLatest(10);

        LogSinkHttpClient.printResponse(getResult);

        if (!getResult.isSuccessful()) {
            System.err.println("ERROR: GET failed.");
            System.exit(1);
        }

        if (getResult.body().contains(runId)) {
            System.out.println();
            System.out.println("OK: Roundtrip message was found.");
            return;
        }

        System.err.println();
        System.err.println("ERROR: Roundtrip message was not found in latest logs.");
        System.exit(1);
    }
}

package de.sasd.logsink.viewer.model;

import com.fasterxml.jackson.annotation.JsonProperty;

/**
 * Repräsentiert eine Logmeldung aus dem PHP-Service.
 *
 * Die Feldnamen orientieren sich bewusst an der JSON-Antwort des PHP-Service.
 */
public record LogEntry(
        Long id,

        @JsonProperty("received_at")
        String receivedAt,

        @JsonProperty("source_ip")
        String sourceIp,

        @JsonProperty("source_port")
        Integer sourcePort,

        @JsonProperty("http_method")
        String httpMethod,

        @JsonProperty("request_uri")
        String requestUri,

        @JsonProperty("content_type")
        String contentType,

        @JsonProperty("user_agent")
        String userAgent,

        @JsonProperty("raw_message_size")
        Long rawMessageSize,

        @JsonProperty("payload_sha256")
        String payloadSha256,

        @JsonProperty("raw_message_text")
        String rawMessageText,

        @JsonProperty("raw_message_base64")
        String rawMessageBase64
) {
    public String preview() {
        String text = firstNonBlank(rawMessageText, rawMessageBase64);

        if (text == null) {
            return "";
        }

        text = text.replace("\r", " ").replace("\n", " ").replace("\t", " ");

        if (text.length() <= 160) {
            return text;
        }

        return text.substring(0, 160) + "...";
    }

    public String fullMessageForDisplay() {
        if (isNotBlank(rawMessageText)) {
            return rawMessageText;
        }

        if (isNotBlank(rawMessageBase64)) {
            return "[base64]\n" + rawMessageBase64;
        }

        return "";
    }

    private static String firstNonBlank(String first, String second) {
        if (isNotBlank(first)) {
            return first;
        }

        if (isNotBlank(second)) {
            return second;
        }

        return null;
    }

    private static boolean isNotBlank(String value) {
        return value != null && !value.isBlank();
    }
}

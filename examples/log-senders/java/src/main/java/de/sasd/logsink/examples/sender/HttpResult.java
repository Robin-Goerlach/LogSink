package de.sasd.logsink.examples.sender;

/**
 * Einfache Response-Struktur für HTTP-Aufrufe.
 *
 * Dieses Beispiel verwendet bewusst keine Frameworks und keine zusätzlichen
 * Bibliotheken. Deshalb reicht ein kleines Record, um Statuscode und Body
 * gemeinsam zurückzugeben.
 */
public record HttpResult(
    int statusCode,
    String body
) {
    /**
     * Prüft, ob der HTTP-Status im erfolgreichen 2xx-Bereich liegt.
     */
    public boolean isSuccessful() {
        return statusCode >= 200 && statusCode < 300;
    }
}

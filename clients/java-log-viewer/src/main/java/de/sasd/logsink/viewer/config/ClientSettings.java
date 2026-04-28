package de.sasd.logsink.viewer.config;

import com.fasterxml.jackson.annotation.JsonIgnoreProperties;

/**
 * Konfiguration des Java-Viewers.
 *
 * Warum gibt es diese Klasse?
 * ---------------------------
 * Bisher stand die Service-URL direkt im Code von LogViewerFrame. Das war für
 * den ersten Test bequem, aber langfristig unpraktisch:
 *
 * - für lokale Entwicklung braucht man eine andere URL als für IONOS,
 * - später kommen vermutlich Token und weitere Einstellungen hinzu,
 * - man sollte nicht neu kompilieren müssen, nur weil sich eine URL ändert.
 *
 * Dieses Record beschreibt deshalb die Werte aus client-settings.json.
 *
 * Die Klasse enthält außerdem "effective..."-Methoden. Diese liefern robuste
 * Werte, auch wenn in der JSON-Datei etwas fehlt oder ein Wert unsinnig ist.
 */
@JsonIgnoreProperties(ignoreUnknown = true)
public record ClientSettings(
    String serviceUrl,
    Integer defaultLimit,
    Integer timeoutSeconds
) {
    public static final String DEFAULT_SERVICE_URL = "http://127.0.0.1:8080/api/logs";
    public static final int DEFAULT_LIMIT = 100;
    public static final int DEFAULT_TIMEOUT_SECONDS = 15;

    /**
     * Liefert die eingebauten Standardwerte.
     *
     * Diese Defaults werden verwendet, wenn keine client-settings.json gefunden
     * wird oder die vorhandene Datei nicht gelesen werden kann.
     */
    public static ClientSettings defaultSettings() {
        return new ClientSettings(
            DEFAULT_SERVICE_URL,
            DEFAULT_LIMIT,
            DEFAULT_TIMEOUT_SECONDS
        );
    }

    /**
     * Normalisiert eine geladene Konfiguration.
     *
     * Dadurch sind die eigentlichen Felder danach nicht mehr null und die Zahlen
     * liegen in einem sinnvollen Bereich.
     */
    public ClientSettings normalized() {
        return new ClientSettings(
            effectiveServiceUrl(),
            effectiveDefaultLimit(),
            effectiveTimeoutSeconds()
        );
    }

    /**
     * Liefert die Service-URL, die in das Eingabefeld des Viewers geschrieben
     * wird.
     */
    public String effectiveServiceUrl() {
        if (serviceUrl == null || serviceUrl.isBlank()) {
            return DEFAULT_SERVICE_URL;
        }

        return serviceUrl.trim();
    }

    /**
     * Liefert die voreingestellte Anzahl der abzurufenden Logmeldungen.
     *
     * Die Begrenzung passt zum aktuellen Service, der ebenfalls maximal 1000
     * Einträge ausliefert.
     */
    public int effectiveDefaultLimit() {
        if (defaultLimit == null) {
            return DEFAULT_LIMIT;
        }

        return Math.max(1, Math.min(1000, defaultLimit));
    }

    /**
     * Liefert das HTTP-Timeout in Sekunden.
     *
     * Sehr kleine Werte führen leicht zu unnötigen Fehlern, sehr große Werte
     * lassen die Anwendung zu lange warten. Für V1 begrenzen wir deshalb auf
     * 1 bis 120 Sekunden.
     */
    public int effectiveTimeoutSeconds() {
        if (timeoutSeconds == null) {
            return DEFAULT_TIMEOUT_SECONDS;
        }

        return Math.max(1, Math.min(120, timeoutSeconds));
    }
}

package de.sasd.logsink.viewer.config;

import com.fasterxml.jackson.databind.DeserializationFeature;
import com.fasterxml.jackson.databind.ObjectMapper;

import java.nio.file.Files;
import java.nio.file.Path;

/**
 * Lädt die Konfiguration des Java-Viewers.
 *
 * Suchreihenfolge:
 *
 * 1. Java-Systemproperty:
 *
 *      -Dlogsink.viewer.config=/pfad/zur/client-settings.json
 *
 * 2. Umgebungsvariable:
 *
 *      LOGSINK_VIEWER_CONFIG=/pfad/zur/client-settings.json
 *
 * 3. Datei im aktuellen Arbeitsverzeichnis:
 *
 *      client-settings.json
 *
 * 4. Datei im Monorepo-Pfad, wenn der Viewer aus dem Repository-Root gestartet
 *    wird:
 *
 *      clients/java-log-viewer/client-settings.json
 *
 * 5. Benutzerbezogene Datei:
 *
 *      ~/.logsink/java-viewer-settings.json
 *
 * Wenn keine Datei gefunden wird, werden eingebaute Defaults verwendet.
 *
 * Warum so viele Orte?
 * --------------------
 * Der Viewer wird in der Praxis unterschiedlich gestartet:
 *
 * - aus dem Client-Verzeichnis,
 * - aus dem Repository-Root,
 * - später vielleicht als installierte JAR von einem beliebigen Ort.
 *
 * Die Suchreihenfolge macht alle drei Fälle nutzbar.
 */
public final class ClientSettingsLoader {

    private static final String SYSTEM_PROPERTY_NAME = "logsink.viewer.config";
    private static final String ENVIRONMENT_VARIABLE_NAME = "LOGSINK_VIEWER_CONFIG";

    private final ObjectMapper objectMapper;

    public ClientSettingsLoader() {
        this.objectMapper = new ObjectMapper()
            .configure(DeserializationFeature.FAIL_ON_UNKNOWN_PROPERTIES, false);
    }

    /**
     * Komfortmethode für den normalen Programmstart.
     */
    public static LoadResult loadDefault() {
        return new ClientSettingsLoader().load();
    }

    /**
     * Lädt die erste gefundene Konfigurationsdatei.
     *
     * Fehler beim Lesen werden nicht als Exception bis zur Oberfläche
     * durchgereicht. Stattdessen liefert LoadResult eine Warnung und robuste
     * Defaultwerte. So startet der Viewer auch dann, wenn die Konfiguration
     * kaputt ist.
     */
    public LoadResult load() {
        String explicitPath = firstNonBlank(
            System.getProperty(SYSTEM_PROPERTY_NAME),
            System.getenv(ENVIRONMENT_VARIABLE_NAME)
        );

        if (explicitPath != null) {
            return loadExplicit(Path.of(explicitPath));
        }

        Path[] candidates = {
            Path.of("client-settings.json"),
            Path.of("clients", "java-log-viewer", "client-settings.json"),
            Path.of(System.getProperty("user.home"), ".logsink", "java-viewer-settings.json")
        };

        for (Path candidate : candidates) {
            Path normalizedCandidate = candidate.toAbsolutePath().normalize();

            if (Files.isRegularFile(normalizedCandidate)) {
                return loadFromExistingFile(normalizedCandidate);
            }
        }

        return new LoadResult(
            ClientSettings.defaultSettings(),
            "built-in defaults",
            null
        );
    }

    private LoadResult loadExplicit(Path path) {
        Path normalizedPath = path.toAbsolutePath().normalize();

        if (!Files.isRegularFile(normalizedPath)) {
            return new LoadResult(
                ClientSettings.defaultSettings(),
                "built-in defaults",
                "Konfigurationsdatei wurde ausdrücklich angegeben, aber nicht gefunden: " + normalizedPath
            );
        }

        return loadFromExistingFile(normalizedPath);
    }

    private LoadResult loadFromExistingFile(Path path) {
        try {
            ClientSettings settings = objectMapper.readValue(path.toFile(), ClientSettings.class);

            if (settings == null) {
                return new LoadResult(
                    ClientSettings.defaultSettings(),
                    "built-in defaults",
                    "Konfigurationsdatei ist leer oder konnte nicht als Objekt gelesen werden: " + path
                );
            }

            return new LoadResult(
                settings.normalized(),
                path.toString(),
                null
            );
        } catch (Exception exception) {
            return new LoadResult(
                ClientSettings.defaultSettings(),
                "built-in defaults",
                "Konfigurationsdatei konnte nicht gelesen werden: " + path + " (" + exception.getMessage() + ")"
            );
        }
    }

    private static String firstNonBlank(String first, String second) {
        if (first != null && !first.isBlank()) {
            return first.trim();
        }

        if (second != null && !second.isBlank()) {
            return second.trim();
        }

        return null;
    }

    /**
     * Ergebnis des Ladevorgangs.
     *
     * settings:
     *   Die nutzbare Konfiguration.
     *
     * sourceDescription:
     *   Woher die Konfiguration stammt. Das ist für Anfänger hilfreich, weil
     *   sofort sichtbar wird, ob wirklich die erwartete Datei genutzt wurde.
     *
     * warning:
     *   Optionale Warnmeldung, falls eine Datei erwartet wurde, aber nicht
     *   gelesen werden konnte.
     */
    public record LoadResult(
        ClientSettings settings,
        String sourceDescription,
        String warning
    ) {
    }
}

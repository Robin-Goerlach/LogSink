package de.sasd.logsink.viewer;

import de.sasd.logsink.viewer.config.ClientSettingsLoader;
import de.sasd.logsink.viewer.ui.LogViewerFrame;

import javax.swing.JOptionPane;
import javax.swing.SwingUtilities;
import javax.swing.UIManager;

/**
 * Einstiegspunkt der Java-Swing-Anwendung.
 *
 * Diese Klasse ist bewusst klein:
 *
 * - Konfiguration laden
 * - Look-and-Feel setzen
 * - Hauptfenster erzeugen
 * - Hauptfenster anzeigen
 *
 * Sie enthält keine HTTP-Logik und keine Tabellenlogik. Diese Aufgaben liegen
 * in LogServiceClient bzw. LogViewerFrame/LogTableModel.
 */
public final class Main {

    private Main() {
        /*
         * Utility-Klasse: keine Instanzen.
         *
         * Main enthält nur die statische main-Methode. Deshalb soll niemand
         * versehentlich new Main() aufrufen.
         */
    }

    public static void main(String[] args) {
        /*
         * Die Konfiguration wird vor dem Start der Oberfläche geladen.
         *
         * Dadurch kann LogViewerFrame sofort mit der richtigen Service-URL,
         * dem voreingestellten Limit und dem HTTP-Timeout erzeugt werden.
         */
        ClientSettingsLoader.LoadResult loadResult = ClientSettingsLoader.loadDefault();

        /*
         * Swing-Regel:
         * ------------
         * Änderungen an Swing-Oberflächen sollen auf dem Event Dispatch Thread
         * passieren. SwingUtilities.invokeLater sorgt dafür.
         */
        SwingUtilities.invokeLater(() -> {
            setSystemLookAndFeel();

            if (loadResult.warning() != null && !loadResult.warning().isBlank()) {
                JOptionPane.showMessageDialog(
                    null,
                    loadResult.warning(),
                    "LogSink Viewer Konfiguration",
                    JOptionPane.WARNING_MESSAGE
                );
            }

            LogViewerFrame frame = new LogViewerFrame(
                loadResult.settings(),
                loadResult.sourceDescription()
            );

            frame.setVisible(true);
        });
    }

    private static void setSystemLookAndFeel() {
        try {
            /*
             * Das System-Look-and-Feel sorgt dafür, dass die Anwendung unter
             * Windows, Linux oder macOS etwas nativer aussieht.
             */
            UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
        } catch (Exception ignored) {
            /*
             * Falls das System-Look-and-Feel nicht verfügbar ist, verwendet
             * Swing automatisch das Standard-Look-and-Feel.
             *
             * Für den Viewer ist das kein schwerer Fehler.
             */
        }
    }
}

package de.sasd.logsink.viewer;

import de.sasd.logsink.viewer.ui.LogViewerFrame;

import javax.swing.SwingUtilities;
import javax.swing.UIManager;

/**
 * Einstiegspunkt der Java-Swing-Anwendung.
 *
 * Diese Klasse ist bewusst klein:
 *
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
         * Swing-Regel:
         * ------------
         * Änderungen an Swing-Oberflächen sollen auf dem Event Dispatch Thread
         * passieren. SwingUtilities.invokeLater sorgt dafür.
         */
        SwingUtilities.invokeLater(() -> {
            setSystemLookAndFeel();

            LogViewerFrame frame = new LogViewerFrame();
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

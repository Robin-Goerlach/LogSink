package de.sasd.logsink.viewer;

import de.sasd.logsink.viewer.ui.LogViewerFrame;

import javax.swing.SwingUtilities;
import javax.swing.UIManager;

/**
 * Einstiegspunkt der Anwendung.
 */
public final class Main {
    private Main() {
        // Utility-Klasse: keine Instanzen.
    }

    public static void main(String[] args) {
        SwingUtilities.invokeLater(() -> {
            setSystemLookAndFeel();

            LogViewerFrame frame = new LogViewerFrame();
            frame.setVisible(true);
        });
    }

    private static void setSystemLookAndFeel() {
        try {
            UIManager.setLookAndFeel(UIManager.getSystemLookAndFeelClassName());
        } catch (Exception ignored) {
            // Falls das System-Look-and-Feel nicht verfügbar ist,
            // verwendet Swing automatisch das Standard-Look-and-Feel.
        }
    }
}

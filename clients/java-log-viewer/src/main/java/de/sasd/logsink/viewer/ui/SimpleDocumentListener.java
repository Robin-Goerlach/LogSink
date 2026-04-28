package de.sasd.logsink.viewer.ui;

import javax.swing.event.DocumentEvent;
import javax.swing.event.DocumentListener;

/**
 * Kleiner Adapter, damit für Textfeldänderungen nur ein Runnable übergeben
 * werden muss.
 *
 * Swing DocumentListener hat drei Methoden:
 *
 * - insertUpdate
 * - removeUpdate
 * - changedUpdate
 *
 * Für unseren Filter ist es egal, welche Art von Änderung passiert ist. Sobald
 * sich der Text ändert, soll applyFilter() ausgeführt werden.
 */
final class SimpleDocumentListener implements DocumentListener {

    private final Runnable onChange;

    SimpleDocumentListener(Runnable onChange) {
        this.onChange = onChange;
    }

    @Override
    public void insertUpdate(DocumentEvent e) {
        onChange.run();
    }

    @Override
    public void removeUpdate(DocumentEvent e) {
        onChange.run();
    }

    @Override
    public void changedUpdate(DocumentEvent e) {
        onChange.run();
    }
}

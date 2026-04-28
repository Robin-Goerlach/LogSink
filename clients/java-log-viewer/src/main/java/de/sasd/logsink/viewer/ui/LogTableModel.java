package de.sasd.logsink.viewer.ui;

import de.sasd.logsink.viewer.model.LogEntry;

import javax.swing.table.AbstractTableModel;
import java.util.ArrayList;
import java.util.List;

/**
 * Tabellenmodell für die Logmeldungen.
 *
 * Swing trennt die Tabelle (JTable) vom Datenmodell (TableModel).
 *
 * JTable ist nur die sichtbare Komponente.
 * LogTableModel sagt der Tabelle:
 *
 * - wie viele Zeilen es gibt,
 * - wie viele Spalten es gibt,
 * - wie die Spalten heißen,
 * - welchen Wert eine bestimmte Zelle hat.
 *
 * Die Spaltentypen sind bewusst gesetzt, damit JTable/TableRowSorter z. B. IDs,
 * Ports und Größen numerisch sortieren kann.
 */
public final class LogTableModel extends AbstractTableModel {

    private static final String[] COLUMN_NAMES = {
        "ID",
        "Empfangen am",
        "IP",
        "Port",
        "Methode",
        "URI",
        "Content-Type",
        "User-Agent",
        "Größe",
        "SHA-256",
        "Meldung"
    };

    private final List<LogEntry> entries = new ArrayList<>();

    /**
     * Ersetzt den kompletten Tabelleninhalt.
     *
     * fireTableDataChanged() informiert JTable, dass sie sich neu zeichnen muss.
     */
    public void setEntries(List<LogEntry> newEntries) {
        entries.clear();

        if (newEntries != null) {
            entries.addAll(newEntries);
        }

        fireTableDataChanged();
    }

    /**
     * Liefert den LogEntry zu einer Modellzeile.
     *
     * Wichtig:
     * --------
     * Wenn die Tabelle sortiert oder gefiltert ist, unterscheidet Swing zwischen
     * View-Zeile und Model-Zeile. LogViewerFrame wandelt deshalb vorher mit
     * table.convertRowIndexToModel(...) um.
     */
    public LogEntry getEntryAt(int modelRow) {
        return entries.get(modelRow);
    }

    @Override
    public int getRowCount() {
        return entries.size();
    }

    @Override
    public int getColumnCount() {
        return COLUMN_NAMES.length;
    }

    @Override
    public String getColumnName(int column) {
        return COLUMN_NAMES[column];
    }

    @Override
    public Class<?> getColumnClass(int columnIndex) {
        return switch (columnIndex) {
            case 0 -> Long.class;
            case 3 -> Integer.class;
            case 8 -> Long.class;
            default -> String.class;
        };
    }

    @Override
    public Object getValueAt(int rowIndex, int columnIndex) {
        LogEntry entry = entries.get(rowIndex);

        return switch (columnIndex) {
            case 0 -> entry.id();
            case 1 -> value(entry.receivedAt());
            case 2 -> value(entry.sourceIp());
            case 3 -> entry.sourcePort();
            case 4 -> value(entry.httpMethod());
            case 5 -> value(entry.requestUri());
            case 6 -> value(entry.contentType());
            case 7 -> value(entry.userAgent());
            case 8 -> entry.rawMessageSize();
            case 9 -> value(entry.payloadSha256());
            case 10 -> entry.preview();
            default -> "";
        };
    }

    private static String value(String value) {
        return value == null ? "" : value;
    }
}

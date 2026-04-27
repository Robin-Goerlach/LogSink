package de.sasd.logsink.viewer.ui;

import de.sasd.logsink.viewer.client.LogServiceClient;
import de.sasd.logsink.viewer.model.LogEntry;

import javax.swing.BorderFactory;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JSpinner;
import javax.swing.JTable;
import javax.swing.JTextField;
import javax.swing.ListSelectionModel;
import javax.swing.SpinnerNumberModel;
import javax.swing.SwingWorker;
import javax.swing.RowFilter;
import javax.swing.table.TableRowSorter;
import java.awt.BorderLayout;
import java.awt.FlowLayout;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.util.List;

/**
 * Hauptfenster des Java-Clients.
 */
public final class LogViewerFrame extends JFrame {
    // private static final String DEFAULT_SERVICE_URL = "http://127.0.0.1:8080/api/logs";
    private static final String DEFAULT_SERVICE_URL = "http://api.sasd.de/logsink/index.php";

    private final LogServiceClient client = new LogServiceClient();
    private final LogTableModel tableModel = new LogTableModel();
    private final JTable table = new JTable(tableModel);
    private final TableRowSorter<LogTableModel> sorter = new TableRowSorter<>(tableModel);

    private final JTextField serviceUrlField = new JTextField(DEFAULT_SERVICE_URL, 45);
    private final JSpinner limitSpinner = new JSpinner(new SpinnerNumberModel(100, 1, 1000, 10));
    private final JTextField filterField = new JTextField(20);
    private final JButton refreshButton = new JButton("Aktualisieren");
    private final JLabel statusLabel = new JLabel("Bereit");

    public LogViewerFrame() {
        super("SASD Log Viewer Java");

        configureFrame();
        configureTable();
        configureActions();

        add(buildTopPanel(), BorderLayout.NORTH);
        add(new JScrollPane(table), BorderLayout.CENTER);
        add(buildStatusPanel(), BorderLayout.SOUTH);
    }

    private void configureFrame() {
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setSize(1300, 720);
        setLocationRelativeTo(null);
        setLayout(new BorderLayout(8, 8));

        ((JPanel) getContentPane()).setBorder(BorderFactory.createEmptyBorder(8, 8, 8, 8));
    }

    private void configureTable() {
        table.setAutoCreateRowSorter(false);
        table.setRowSorter(sorter);
        table.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
        table.setAutoResizeMode(JTable.AUTO_RESIZE_OFF);

        table.getColumnModel().getColumn(0).setPreferredWidth(70);
        table.getColumnModel().getColumn(1).setPreferredWidth(190);
        table.getColumnModel().getColumn(2).setPreferredWidth(150);
        table.getColumnModel().getColumn(3).setPreferredWidth(70);
        table.getColumnModel().getColumn(4).setPreferredWidth(90);
        table.getColumnModel().getColumn(5).setPreferredWidth(220);
        table.getColumnModel().getColumn(6).setPreferredWidth(230);
        table.getColumnModel().getColumn(7).setPreferredWidth(220);
        table.getColumnModel().getColumn(8).setPreferredWidth(90);
        table.getColumnModel().getColumn(9).setPreferredWidth(520);
        table.getColumnModel().getColumn(10).setPreferredWidth(500);

        table.addMouseListener(new MouseAdapter() {
            @Override
            public void mouseClicked(MouseEvent event) {
                if (event.getClickCount() == 2 && table.getSelectedRow() >= 0) {
                    showSelectedLogEntry();
                }
            }
        });
    }

    private void configureActions() {
        refreshButton.addActionListener(event -> refreshLogs());

        filterField.addActionListener(event -> applyFilter());
        filterField.getDocument().addDocumentListener(new SimpleDocumentListener(this::applyFilter));
    }

    private JPanel buildTopPanel() {
        JPanel panel = new JPanel(new FlowLayout(FlowLayout.LEFT));

        panel.add(new JLabel("Service-URL:"));
        panel.add(serviceUrlField);
        panel.add(new JLabel("Limit:"));
        panel.add(limitSpinner);
        panel.add(refreshButton);
        panel.add(new JLabel("Filter:"));
        panel.add(filterField);

        return panel;
    }

    private JPanel buildStatusPanel() {
        JPanel panel = new JPanel(new BorderLayout());
        panel.setBorder(BorderFactory.createEmptyBorder(6, 0, 0, 0));
        panel.add(statusLabel, BorderLayout.CENTER);

        return panel;
    }

    private void refreshLogs() {
        refreshButton.setEnabled(false);
        statusLabel.setText("Lade Logmeldungen ...");

        String serviceUrl = serviceUrlField.getText();
        int limit = (Integer) limitSpinner.getValue();

        SwingWorker<List<LogEntry>, Void> worker = new SwingWorker<>() {
            @Override
            protected List<LogEntry> doInBackground() throws Exception {
                return client.fetchLatestLogs(serviceUrl, limit);
            }

            @Override
            protected void done() {
                try {
                    List<LogEntry> entries = get();
                    tableModel.setEntries(entries);
                    applyFilter();
                    statusLabel.setText(entries.size() + " Logmeldungen geladen.");
                } catch (Exception exception) {
                    statusLabel.setText("Fehler: " + exception.getMessage());
                } finally {
                    refreshButton.setEnabled(true);
                }
            }
        };

        worker.execute();
    }

    private void showSelectedLogEntry() {
        int viewRow = table.getSelectedRow();

        if (viewRow < 0) {
            return;
        }

        int modelRow = table.convertRowIndexToModel(viewRow);
        LogEntry entry = tableModel.getEntryAt(modelRow);

        LogDetailDialog dialog = new LogDetailDialog(this, entry);
        dialog.setVisible(true);
    }

    private void applyFilter() {
        String filterText = filterField.getText();

        if (filterText == null || filterText.isBlank()) {
            sorter.setRowFilter(null);
            return;
        }

        sorter.setRowFilter(RowFilter.regexFilter("(?i)" + java.util.regex.Pattern.quote(filterText.trim())));
    }
}

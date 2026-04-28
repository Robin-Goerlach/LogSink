package de.sasd.logsink.viewer.ui;

import de.sasd.logsink.viewer.client.LogServiceClient;
import de.sasd.logsink.viewer.config.ClientSettings;
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
 *
 * Diese Klasse verbindet:
 *
 * - Eingabefelder für Service-URL, Limit und Filter,
 * - JTable zur Anzeige der Logmeldungen,
 * - LogServiceClient für HTTP-Abrufe,
 * - LogTableModel als Datenmodell der Tabelle,
 * - LogDetailDialog für Doppelklick-Details.
 *
 * Die Service-URL wird nicht mehr hart im Code festgelegt. Sie kommt jetzt aus
 * ClientSettings, die wiederum aus client-settings.json geladen werden können.
 */
public final class LogViewerFrame extends JFrame {

    private final LogServiceClient client;
    private final LogTableModel tableModel = new LogTableModel();
    private final JTable table = new JTable(tableModel);
    private final TableRowSorter<LogTableModel> sorter = new TableRowSorter<>(tableModel);

    private final JTextField serviceUrlField;
    private final JSpinner limitSpinner;
    private final JTextField filterField = new JTextField(20);
    private final JButton refreshButton = new JButton("Aktualisieren");
    private final JLabel statusLabel;

    public LogViewerFrame(ClientSettings settings, String settingsSourceDescription) {
        super("SASD Log Viewer Java");

        /*
         * Der HTTP-Client bekommt das Timeout aus der Konfiguration.
         *
         * Dadurch muss später nicht im Code geändert werden, wenn ein langsamer
         * Server oder eine entfernte Hosting-Umgebung mehr Zeit braucht.
         */
        this.client = new LogServiceClient(settings.effectiveTimeoutSeconds());

        /*
         * Die Oberfläche zeigt die geladene Service-URL an. Der Benutzer kann
         * sie weiterhin direkt im UI ändern. Das ist praktisch für schnelle
         * Tests, ersetzt aber nicht die dauerhafte Konfiguration in JSON.
         */
        this.serviceUrlField = new JTextField(settings.effectiveServiceUrl(), 45);

        this.limitSpinner = new JSpinner(new SpinnerNumberModel(
            settings.effectiveDefaultLimit(),
            1,
            1000,
            10
        ));

        this.statusLabel = new JLabel("Bereit - Konfiguration: " + settingsSourceDescription);

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
        /*
         * Wir verwenden bewusst einen eigenen TableRowSorter, damit Sortierung
         * und Filterung kontrolliert über unser LogTableModel laufen.
         */
        table.setAutoCreateRowSorter(false);
        table.setRowSorter(sorter);

        table.setSelectionMode(ListSelectionModel.SINGLE_SELECTION);
        table.setAutoResizeMode(JTable.AUTO_RESIZE_OFF);

        /*
         * Spaltenbreiten sind nur UI-Komfort. Sie ändern keine Datenlogik.
         */
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

        /*
         * Doppelklick auf eine Tabellenzeile öffnet den Detaildialog.
         */
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

        /*
         * Enter im Filterfeld und jede Textänderung aktualisieren den Filter.
         */
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

    /**
     * Lädt Logmeldungen vom Service.
     *
     * Wichtig:
     * --------
     * HTTP-Zugriffe dürfen nicht direkt auf dem Swing-UI-Thread laufen, weil die
     * Oberfläche sonst während langsamer Netzwerkzugriffe einfrieren würde.
     *
     * Deshalb verwendet diese Methode SwingWorker.
     */
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

        /*
         * Die Tabelle kann sortiert oder gefiltert sein. Deshalb ist die
         * angeklickte View-Zeile nicht zwingend dieselbe Zeile im TableModel.
         */
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

        /*
         * regexFilter erwartet einen regulären Ausdruck.
         *
         * Pattern.quote sorgt dafür, dass Suchtexte wie "." oder "[" nicht als
         * Regex-Sonderzeichen interpretiert werden.
         *
         * (?i) macht die Suche unabhängig von Groß-/Kleinschreibung.
         */
        sorter.setRowFilter(RowFilter.regexFilter("(?i)" + java.util.regex.Pattern.quote(filterText.trim())));
    }
}

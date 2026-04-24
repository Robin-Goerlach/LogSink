package de.sasd.logsink.viewer.ui;

import de.sasd.logsink.viewer.model.LogEntry;

import javax.swing.BorderFactory;
import javax.swing.JDialog;
import javax.swing.JFrame;
import javax.swing.JScrollPane;
import javax.swing.JTextArea;
import java.awt.BorderLayout;
import java.awt.Dimension;

/**
 * Einfacher Detaildialog für eine Logmeldung.
 */
public final class LogDetailDialog extends JDialog {
    public LogDetailDialog(JFrame owner, LogEntry entry) {
        super(owner, "Logmeldung " + entry.id(), true);

        JTextArea textArea = new JTextArea(buildText(entry));
        textArea.setEditable(false);
        textArea.setLineWrap(false);
        textArea.setWrapStyleWord(false);
        textArea.setBorder(BorderFactory.createEmptyBorder(8, 8, 8, 8));

        JScrollPane scrollPane = new JScrollPane(textArea);
        scrollPane.setPreferredSize(new Dimension(900, 600));

        setLayout(new BorderLayout());
        add(scrollPane, BorderLayout.CENTER);

        pack();
        setLocationRelativeTo(owner);
    }

    private String buildText(LogEntry entry) {
        StringBuilder builder = new StringBuilder();

        builder.append("ID: ").append(entry.id()).append("\n");
        builder.append("Empfangen am: ").append(nullToEmpty(entry.receivedAt())).append("\n");
        builder.append("Quelle: ").append(nullToEmpty(entry.sourceIp()));

        if (entry.sourcePort() != null) {
            builder.append(":").append(entry.sourcePort());
        }

        builder.append("\n");
        builder.append("HTTP-Methode: ").append(nullToEmpty(entry.httpMethod())).append("\n");
        builder.append("URI: ").append(nullToEmpty(entry.requestUri())).append("\n");
        builder.append("Content-Type: ").append(nullToEmpty(entry.contentType())).append("\n");
        builder.append("User-Agent: ").append(nullToEmpty(entry.userAgent())).append("\n");
        builder.append("Größe: ").append(entry.rawMessageSize() == null ? "" : entry.rawMessageSize()).append("\n");
        builder.append("SHA-256: ").append(nullToEmpty(entry.payloadSha256())).append("\n");

        builder.append("\n");
        builder.append("--------------------------------------------------------------------------------\n");
        builder.append("Meldung\n");
        builder.append("--------------------------------------------------------------------------------\n");
        builder.append(entry.fullMessageForDisplay());

        return builder.toString();
    }

    private static String nullToEmpty(String value) {
        return value == null ? "" : value;
    }
}

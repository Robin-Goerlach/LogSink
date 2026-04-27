-- ============================================================================
-- SASD Logging Service - MariaDB 10.11 Demo Database
-- Datei: sasd_logging_mariadb_10_11_demo.sql
--
-- Inhalt:
--   - Tabelle log_entries
--   - Trigger zur automatischen Ermittlung von Payload-Größe und SHA-256-Hash
--   - View v_log_entries
--   - 10 Demo-Logmeldungen
--
-- Hinweis:
--   Diese Demo-Datei enthält bewusst keine Zugriffsschutz-Logik.
--   Für Produktivbetrieb sollte der PHP-Service dennoch authentifiziert und
--   die Datenbank nur intern erreichbar sein.
-- ============================================================================

USE sasd_logging;

-- ----------------------------------------------------------------------------
-- Tabelle für unveränderte Logmeldungen
-- ----------------------------------------------------------------------------

CREATE TABLE IF NOT EXISTS log_entries (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    received_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),

    -- Unveränderte Logmeldung bzw. unveränderter HTTP-Request-Body.
    -- LONGBLOB ist absichtlich gewählt, damit auch Binärdaten oder ungültiges
    -- UTF-8 unverändert gespeichert werden können.
    raw_message LONGBLOB NOT NULL,

    -- Technische Metadaten des Requests
    source_ip VARCHAR(45) NULL,
    source_port INT UNSIGNED NULL,
    http_method VARCHAR(16) NULL,
    request_uri TEXT NULL,
    content_type VARCHAR(255) NULL,
    user_agent TEXT NULL,

    -- Optional: HTTP-Header als JSON-Text
    request_headers_json LONGTEXT NULL
      CHECK (request_headers_json IS NULL OR JSON_VALID(request_headers_json)),

    -- Technische Hilfsfelder; werden durch den Trigger gesetzt.
    raw_message_size BIGINT UNSIGNED NULL,
    payload_sha256 CHAR(64) NULL,

    PRIMARY KEY (id),

    INDEX idx_received_at (received_at),
    INDEX idx_source_ip_received_at (source_ip, received_at),
    INDEX idx_payload_sha256 (payload_sha256)

) ENGINE=InnoDB
  ROW_FORMAT=DYNAMIC
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Trigger
-- ----------------------------------------------------------------------------

DROP TRIGGER IF EXISTS trg_log_entries_before_insert;

DELIMITER //

CREATE TRIGGER trg_log_entries_before_insert
BEFORE INSERT ON log_entries
FOR EACH ROW
BEGIN
    SET NEW.raw_message_size = OCTET_LENGTH(NEW.raw_message);
    SET NEW.payload_sha256 = SHA2(NEW.raw_message, 256);
END//

DELIMITER ;

-- ----------------------------------------------------------------------------
-- Lesefreundliches View
-- ----------------------------------------------------------------------------

CREATE OR REPLACE VIEW v_log_entries AS
SELECT
    id,
    received_at,
    source_ip,
    source_port,
    http_method,
    request_uri,
    content_type,
    user_agent,
    request_headers_json,
    raw_message_size,
    payload_sha256,
    TO_BASE64(raw_message) AS raw_message_base64,
    CASE
        WHEN content_type LIKE 'text/%'
          OR content_type LIKE 'application/json%'
          OR content_type LIKE 'application/xml%'
          OR content_type LIKE 'application/x-www-form-urlencoded%'
        THEN CONVERT(raw_message USING utf8mb4)
        ELSE NULL
    END AS raw_message_text
FROM log_entries;


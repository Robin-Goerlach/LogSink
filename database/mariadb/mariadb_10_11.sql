-- ============================================================================
-- SASD Logging Service - MariaDB 10.11 Demo Database
-- Datei: sasd_logging_mariadb_10_11_demo.sql
--
-- Inhalt:
--   - Datenbank sasd_logging
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

CREATE DATABASE IF NOT EXISTS sasd_logging
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

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

-- ----------------------------------------------------------------------------
-- Demo-Logmeldungen
-- ----------------------------------------------------------------------------
-- Die Demo-Daten werden bei jedem Import erneut eingefügt.
-- Falls dies nicht gewünscht ist, den folgenden DELETE-Befehl aktivieren.
--
-- DELETE FROM log_entries;

INSERT INTO log_entries (
    raw_message,
    source_ip,
    source_port,
    http_method,
    request_uri,
    content_type,
    user_agent,
    request_headers_json
) VALUES
(
    CAST('{"timestamp":"2026-04-24T09:15:01+02:00","level":"INFO","service":"auth-service","message":"User login successful","context":{"userId":1001,"username":"demo.user"}}' AS BINARY),
    '192.168.10.21',
    53214,
    'POST',
    '/api/logs',
    'application/json; charset=utf-8',
    'SASD-DemoClient/1.0',
    '{"Host":"logging.local","X-Request-Id":"demo-0001","Content-Type":"application/json; charset=utf-8"}'
),
(
    CAST('{"timestamp":"2026-04-24T09:16:42+02:00","level":"WARN","service":"billing-service","message":"Payment provider responded slowly","context":{"durationMs":2840,"provider":"DemoPay"}}' AS BINARY),
    '192.168.10.22',
    53215,
    'POST',
    '/api/logs',
    'application/json; charset=utf-8',
    'SASD-DemoClient/1.0',
    '{"Host":"logging.local","X-Request-Id":"demo-0002","Content-Type":"application/json; charset=utf-8"}'
),
(
    CAST('{"timestamp":"2026-04-24T09:17:10+02:00","level":"ERROR","service":"profile-service","message":"Database query failed","context":{"sqlState":"HY000","errorCode":1205,"operation":"updateProfile"}}' AS BINARY),
    '192.168.10.23',
    53216,
    'POST',
    '/api/logs',
    'application/json; charset=utf-8',
    'SASD-DemoClient/1.0',
    '{"Host":"logging.local","X-Request-Id":"demo-0003","Content-Type":"application/json; charset=utf-8"}'
),
(
    CAST('{"timestamp":"2026-04-24T09:18:33+02:00","level":"DEBUG","service":"project-service","message":"Repository method entered","context":{"class":"ProjectRepository","method":"findByOwnerId","ownerId":1001}}' AS BINARY),
    '192.168.10.24',
    53217,
    'POST',
    '/api/logs',
    'application/json; charset=utf-8',
    'SASD-DemoClient/1.0',
    '{"Host":"logging.local","X-Request-Id":"demo-0004","Content-Type":"application/json; charset=utf-8"}'
),
(
    CAST('2026-04-24 09:19:58 INFO legacy-client Backup job started for volume /srv/data' AS BINARY),
    '192.168.10.30',
    54100,
    'POST',
    '/api/logs/raw',
    'text/plain; charset=utf-8',
    'LegacyShellLogger/0.9',
    '{"Host":"logging.local","X-Request-Id":"demo-0005","Content-Type":"text/plain; charset=utf-8"}'
),
(
    CAST('2026-04-24 09:23:11 ERROR legacy-client Backup job failed: no space left on device' AS BINARY),
    '192.168.10.30',
    54101,
    'POST',
    '/api/logs/raw',
    'text/plain; charset=utf-8',
    'LegacyShellLogger/0.9',
    '{"Host":"logging.local","X-Request-Id":"demo-0006","Content-Type":"text/plain; charset=utf-8"}'
),
(
    CAST('{"timestamp":"2026-04-24T09:25:04+02:00","level":"INFO","service":"gateway","message":"Incoming request","context":{"method":"GET","path":"/api/projects","status":200,"durationMs":37}}' AS BINARY),
    '2001:db8:85a3::8a2e:370:7334',
    61234,
    'POST',
    '/api/logs',
    'application/json; charset=utf-8',
    'SASD-Gateway/2.1',
    '{"Host":"logging.local","X-Request-Id":"demo-0007","Content-Type":"application/json; charset=utf-8","X-Forwarded-For":"2001:db8:85a3::8a2e:370:7334"}'
),
(
    CAST('{"timestamp":"2026-04-24T09:27:49+02:00","level":"SECURITY","service":"auth-service","message":"Repeated failed login attempts","context":{"username":"admin","attempts":5,"source":"192.168.10.99"}}' AS BINARY),
    '192.168.10.99',
    49888,
    'POST',
    '/api/logs',
    'application/json; charset=utf-8',
    'SASD-DemoClient/1.0',
    '{"Host":"logging.local","X-Request-Id":"demo-0008","Content-Type":"application/json; charset=utf-8"}'
),
(
    CAST('{"timestamp":"2026-04-24T09:30:12+02:00","level":"INFO","service":"scheduler","message":"Daily cleanup completed","context":{"deletedRows":128,"durationMs":412}}' AS BINARY),
    '192.168.10.40',
    55001,
    'POST',
    '/api/logs',
    'application/json; charset=utf-8',
    'SASD-Scheduler/1.3',
    '{"Host":"logging.local","X-Request-Id":"demo-0009","Content-Type":"application/json; charset=utf-8"}'
),
(
    CAST('{"timestamp":"2026-04-24T09:32:55+02:00","level":"FATAL","service":"reporting-service","message":"Unhandled exception","context":{"exception":"RuntimeException","file":"ReportGenerator.php","line":214}}' AS BINARY),
    '192.168.10.41',
    55002,
    'POST',
    '/api/logs',
    'application/json; charset=utf-8',
    'SASD-Reporting/0.8',
    '{"Host":"logging.local","X-Request-Id":"demo-0010","Content-Type":"application/json; charset=utf-8"}'
);

-- ----------------------------------------------------------------------------
-- Beispielabfrage
-- ----------------------------------------------------------------------------

SELECT
    id,
    received_at,
    source_ip,
    http_method,
    request_uri,
    content_type,
    raw_message_size,
    payload_sha256,
    raw_message_text
FROM v_log_entries
ORDER BY id DESC
LIMIT 10;

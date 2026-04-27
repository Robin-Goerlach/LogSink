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


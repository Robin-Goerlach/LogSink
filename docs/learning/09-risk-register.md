# Risk Register

## R-001: Zu großer Umbau in einem Schritt

**Risiko:** Service, Datenbank und Client werden gleichzeitig so stark verändert, dass Fehler schwer lokalisierbar sind.

**Maßnahme:** Atomare Schritte. Jeder Schritt mit Test und Commit.

## R-002: Alte und neue API werden verwechselt

**Risiko:** `/api/logs` und `/api/v1/...` liefern unterschiedliche Formate.

**Maßnahme:** API-Vertrag dokumentieren, Legacy explizit markieren, Java-Client bewusst umstellen.

## R-003: Tokenmodell wird unsauber

**Risiko:** Ein Token darf versehentlich lesen und schreiben.

**Maßnahme:** Principal-Typen `source` und `client` streng trennen. Scopes prüfen.

## R-004: Tokens landen in Logs

**Risiko:** Lokales Service- oder Client-Logging protokolliert Authorization-Header.

**Maßnahme:** Token-Sanitizing in Service und Client.

## R-005: Datenbank-DDL wird aus Ziel-Dokumenten geraten

**Risiko:** Tabellen werden erfunden, ohne klaren Projektbezug.

**Maßnahme:** Eigenes, bewusst dokumentiertes Schema entwerfen und Entscheidungen im Decision Log festhalten.

## R-006: IP-Allowlisting funktioniert lokal, aber nicht hinter Proxy/NAT

**Risiko:** Server sieht andere IP als erwartet.

**Maßnahme:** REMOTE_ADDR dokumentieren, Proxy-Thema gesondert behandeln, IPv6-Grenzen markieren.

## R-007: File-basiertes Rate-Limiting ist bei mehreren Instanzen begrenzt

**Risiko:** Bei mehreren Serverinstanzen funktioniert Rate-Limiting nicht global.

**Maßnahme:** Für V1 akzeptieren und dokumentieren; später DB/Redis-basiertes Verfahren prüfen.

## R-008: IONOS-Hosting unterscheidet sich von lokaler Umgebung

**Risiko:** Pfade, PHP-Version, DocumentRoot oder Schreibrechte passen nicht.

**Maßnahme:** IONOS-Variante klären, Deployment-Checkliste erstellen.

## R-009: Java-Client blockiert UI bei langsamen Requests

**Risiko:** UI friert ein.

**Maßnahme:** SwingWorker beibehalten/ausbauen, Timeouts konfigurieren.

## R-010: Anfänger-Dokumentation wird vergessen

**Risiko:** Code funktioniert, aber Nutzer versteht Start, Tests und Fehleranalyse nicht.

**Maßnahme:** Bei jedem Schritt Beginner-Hinweise ergänzen.

## R-011: Composer-Autoloading bricht bestehenden Fallback

**Risiko:** Service startet nach Composer-Umstellung nicht mehr.

**Maßnahme:** Composer in eigenem Schritt einführen, Starttest sofort danach.

## R-012: Java-Client erwartet vollständige View-Felder

**Risiko:** Client bricht, wenn Server weniger Felder liefert.

**Maßnahme:** Client robust gegenüber optionalen Feldern machen.

## R-013: Export von Daten enthält sensible Inhalte

**Risiko:** CSV/JSON-Export gibt Tokens oder zu viele Details preis.

**Maßnahme:** Export bewusst auf geladene Logdaten beschränken, Tokens niemals exportieren.

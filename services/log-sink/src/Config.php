<?php

declare(strict_types=1);

namespace Sasd\LogSink;

/**
 * Kleine Konfigurationsklasse für den V1-Service.
 *
 * Diese Klasse kapselt den Zugriff auf Werte aus .env bzw. .env-logsink.
 *
 * Warum gibt es dafür eine eigene Klasse?
 * --------------------------------------
 * Der Rest des Codes soll nicht direkt parse_ini_file(), $_ENV oder getenv()
 * benutzen müssen. Stattdessen fragt der Code z. B.:
 *
 *   $config->string('DB_HOST', '127.0.0.1')
 *
 * Dadurch sieht man an der Aufrufstelle sofort:
 *
 * - welcher Konfigurationswert gebraucht wird,
 * - welcher Defaultwert gilt,
 * - welcher Typ erwartet wird.
 *
 * Für später:
 * -----------
 * Wenn Composer, dotenv-Library, Validierung oder verschlüsselte Secrets
 * eingeführt werden, kann viel davon hier gekapselt werden.
 */
final class Config
{
    /**
     * @param array<string, mixed> $values Werte aus der Konfigurationsdatei.
     */
    private function __construct(
        private readonly array $values
    ) {
    }

    /**
     * Lädt eine INI-artige Konfigurationsdatei.
     *
     * Die aktuelle .env-Datei ist bewusst einfach aufgebaut:
     *
     *   DB_HOST=127.0.0.1
     *   APP_DEBUG=true
     *
     * INI_SCANNER_TYPED sorgt dafür, dass einfache Werte wie true/false oder
     * Zahlen schon etwas sinnvoller typisiert werden.
     *
     * Falls die Datei fehlt oder nicht gelesen werden kann, wird keine Exception
     * geworfen. Stattdessen entsteht eine leere Konfiguration. Die Anwendung
     * arbeitet dann mit Defaultwerten weiter.
     */
    public static function fromEnvFile(string $path): self
    {
        if (!is_file($path)) {
            return new self([]);
        }

        $values = parse_ini_file($path, false, INI_SCANNER_TYPED);

        if (!is_array($values)) {
            return new self([]);
        }

        return new self($values);
    }

    /**
     * Liest einen Konfigurationswert als String.
     *
     * Auch Zahlen und Booleans dürfen hier zu Strings werden, weil parse_ini_file
     * solche Werte durch INI_SCANNER_TYPED bereits umgewandelt haben kann.
     */
    public function string(string $name, string $default = ''): string
    {
        $value = $this->values[$name] ?? $default;

        return is_string($value) || is_numeric($value) || is_bool($value)
            ? (string) $value
            : $default;
    }

    /**
     * Liest einen Konfigurationswert als Integer.
     *
     * Beispiel:
     *
     *   DB_PORT=3306
     */
    public function int(string $name, int $default = 0): int
    {
        $value = $this->values[$name] ?? $default;

        return is_numeric($value) ? (int) $value : $default;
    }

    /**
     * Liest einen Konfigurationswert als Boolean.
     *
     * Unterstützt werden neben echten Booleans auch typische Textwerte:
     *
     *   true, yes, on, 1
     *
     * Alles andere wird defensiv interpretiert.
     */
    public function bool(string $name, bool $default = false): bool
    {
        $value = $this->values[$name] ?? $default;

        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
        }

        return (bool) $value;
    }
}

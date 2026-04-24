<?php

declare(strict_types=1);

namespace Sasd\LogSink;

final class Config
{
    /**
     * @param array<string, mixed> $values
     */
    private function __construct(
        private readonly array $values
    ) {
    }

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

    public function string(string $name, string $default = ''): string
    {
        $value = $this->values[$name] ?? $default;

        return is_string($value) || is_numeric($value) || is_bool($value)
            ? (string) $value
            : $default;
    }

    public function int(string $name, int $default = 0): int
    {
        $value = $this->values[$name] ?? $default;

        return is_numeric($value) ? (int) $value : $default;
    }

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

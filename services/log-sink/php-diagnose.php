<?php

declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

echo "PHP_VERSION=" . PHP_VERSION . PHP_EOL;
echo "PHP_SAPI=" . PHP_SAPI . PHP_EOL;
echo "__DIR__=" . __DIR__ . PHP_EOL;

echo "pdo_mysql=" . (extension_loaded('pdo_mysql') ? 'yes' : 'no') . PHP_EOL;

echo ".env exists=" . (is_file(__DIR__ . '/.env') ? 'yes' : 'no') . PHP_EOL;
echo "src/Bootstrap.php exists=" . (is_file(__DIR__ . '/src/Bootstrap.php') ? 'yes' : 'no') . PHP_EOL;
echo "public/index.php exists=" . (is_file(__DIR__ . '/public/index.php') ? 'yes' : 'no') . PHP_EOL;
echo "var/log exists=" . (is_dir(__DIR__ . '/var/log') ? 'yes' : 'no') . PHP_EOL;
echo "var/log writable=" . (is_writable(__DIR__ . '/var/log') ? 'yes' : 'no') . PHP_EOL;

$envFile = __DIR__ . '/.env';

if (!is_file($envFile)) {
    echo PHP_EOL . "No .env file found." . PHP_EOL;
    exit;
}

$env = parse_ini_file($envFile, false, INI_SCANNER_TYPED);

if (!is_array($env)) {
    echo PHP_EOL . ".env could not be parsed." . PHP_EOL;
    exit;
}

echo PHP_EOL;
echo "DB_HOST=" . ($env['DB_HOST'] ?? 'missing') . PHP_EOL;
echo "DB_PORT=" . ($env['DB_PORT'] ?? 'missing') . PHP_EOL;
echo "DB_DATABASE=" . ($env['DB_DATABASE'] ?? 'missing') . PHP_EOL;
echo "DB_USERNAME=" . ($env['DB_USERNAME'] ?? 'missing') . PHP_EOL;
echo "DB_PASSWORD_SET=" . (!empty($env['DB_PASSWORD']) ? 'yes' : 'no') . PHP_EOL;

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
        $env['DB_HOST'] ?? '',
        (int) ($env['DB_PORT'] ?? 3306),
        $env['DB_DATABASE'] ?? ''
    );

    $pdo = new PDO(
        $dsn,
        (string) ($env['DB_USERNAME'] ?? ''),
        (string) ($env['DB_PASSWORD'] ?? ''),
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]
    );

    echo PHP_EOL . "DB_CONNECTION=ok" . PHP_EOL;

    $stmt = $pdo->query('SELECT COUNT(*) AS count_logs FROM log_entries');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "LOG_ENTRIES=" . ($row['count_logs'] ?? 'unknown') . PHP_EOL;
} catch (Throwable $exception) {
    echo PHP_EOL . "DB_CONNECTION=failed" . PHP_EOL;
    echo "ERROR=" . $exception->getMessage() . PHP_EOL;
}

<?php
/**
 * Временная диагностика. Залей в public_html, открой в браузере, потом УДАЛИ.
 */
header('Content-Type: text/plain; charset=utf-8');

echo "PHP: " . PHP_VERSION . "\n";
echo "mbstring: " . (extension_loaded('mbstring') ? 'yes' : 'NO') . "\n";
echo "pdo_mysql: " . (extension_loaded('pdo_mysql') ? 'yes' : 'NO') . "\n\n";

$configPath = __DIR__ . '/includes/config.php';
if (!is_file($configPath)) {
    echo "config.php: MISSING\n";
    exit;
}
$config = require $configPath;
echo "db_host: {$config['db_host']}\n";
echo "db_port: " . ($config['db_port'] ?? '(default)') . "\n";
echo "db_name: {$config['db_name']}\n";
echo "db_user: {$config['db_user']}\n";
echo "db_pass: " . (isset($config['db_pass']) && $config['db_pass'] !== '' ? '(set)' : '(EMPTY)') . "\n\n";

try {
    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $config['db_host'],
        $config['db_name'],
        $config['db_charset'] ?? 'utf8mb4'
    );
    if (!empty($config['db_port'])) {
        $dsn .= ';port=' . (int) $config['db_port'];
    }
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    echo "DB connect: OK\n";
    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables: " . (count($tables) ? implode(', ', $tables) : '(none)') . "\n";
    if (in_array('users', $tables, true)) {
        $n = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
        echo "users rows: {$n}\n";
    }
    if (in_array('materials', $tables, true)) {
        $n = (int) $pdo->query('SELECT COUNT(*) FROM materials')->fetchColumn();
        echo "materials rows: {$n}\n";
    }
} catch (Throwable $e) {
    echo "DB connect: FAIL\n";
    echo $e->getMessage() . "\n";
}

<?php
/**
 * Простые smoke-тесты без базы данных (для GitHub Actions).
 * Запуск: php tests/smoke.php
 */

declare(strict_types=1);

$failed = 0;

function assert_true(bool $cond, string $message): void
{
    global $failed;
    if ($cond) {
        echo "[OK] $message\n";
        return;
    }
    echo "[FAIL] $message\n";
    $failed++;
}

require_once dirname(__DIR__) . '/includes/validation.php';

// --- validateLoginInput ---
$empty = validateLoginInput('', '');
assert_true(count($empty['errors']) >= 2, 'пустой логин и пароль дают ошибки');

$okLogin = validateLoginInput('admin', 'secret');
assert_true($okLogin['errors'] === [], 'корректные логин/пароль без ошибок формата');
assert_true($okLogin['login'] === 'admin', 'логин возвращается trimmed');

$long = validateLoginInput(str_repeat('a', 51), 'x');
assert_true(count($long['errors']) >= 1, 'слишком длинный логин отклоняется');

// --- validateMaterialInput с PDO-заглушкой не гоняем: нужен MySQL.
// Проверяем, что файлы критичных точек существуют.
$required = [
    'index.php',
    'welcome.php',
    'material.php',
    'includes/db.php',
    'includes/auth.php',
    'includes/user_auth.php',
    'includes/validation.php',
    'admin/login.php',
    'sql/schema.sql',
    'sql/seed.sql',
];

foreach ($required as $path) {
    $full = dirname(__DIR__) . '/' . $path;
    assert_true(is_file($full), "есть файл $path");
}

if ($failed > 0) {
    echo "\nFailed: $failed\n";
    exit(1);
}

echo "\nAll smoke tests passed.\n";
exit(0);

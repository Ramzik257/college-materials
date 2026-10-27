<?php
/**
 * Роутер для PHP built-in server:
 * php -S 127.0.0.1:8080 router.php
 * Корневой URL открывает welcome.php
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path === '/' || $path === '') {
    require __DIR__ . '/welcome.php';
    return true;
}

if ($path !== '/' && is_file($file)) {
    return false;
}

http_response_code(404);
echo 'Страница не найдена';
return true;

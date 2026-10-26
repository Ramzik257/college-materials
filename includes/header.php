<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Учебные материалы';
}
if (!isset($pageDescription)) {
    $pageDescription = 'Каталог учебных материалов для студентов колледжа';
}

if (!function_exists('isAdminLoggedIn')) {
    require_once __DIR__ . '/auth.php';
}
$adminLoggedIn = isAdminLoggedIn();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="container">
    <nav>
        <ul>
            <li><strong><a href="/index.php">Учебные материалы</a></strong></li>
        </ul>
        <ul>
            <li><a href="/index.php">Каталог</a></li>
            <?php if ($adminLoggedIn): ?>
                <li><a href="/admin/index.php">Панель</a></li>
                <li><a href="/admin/logout.php">Выйти</a></li>
            <?php else: ?>
                <li><a href="/admin/login.php">Админ</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main class="container">

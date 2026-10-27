<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Campus Notes';
}
if (!isset($pageDescription)) {
    $pageDescription = 'Каталог учебных материалов для студентов колледжа';
}
if (!isset($isAuthPage)) {
    $isAuthPage = false;
}

if (!function_exists('isAdminLoggedIn')) {
    require_once __DIR__ . '/auth.php';
}
if (!function_exists('isUserLoggedIn')) {
    require_once __DIR__ . '/user_auth.php';
}

$adminLoggedIn = isAdminLoggedIn();
$userLoggedIn = isUserLoggedIn();
$guestBrowse = isGuestBrowse();
$userName = currentUserName();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="<?= $isAuthPage ? 'page-auth' : 'page-app' ?>">
<?php if (!$isAuthPage): ?>
<header class="site-header">
    <div class="shell header-row">
        <a class="logo" href="/index.php">Campus&nbsp;Notes</a>
        <nav class="site-nav" aria-label="Основная">
            <a href="/index.php">Каталог</a>
            <?php if ($adminLoggedIn): ?>
                <a href="/admin/index.php">Панель</a>
                <a href="/admin/logout.php">Выйти (админ)</a>
            <?php else: ?>
                <a class="nav-quiet" href="/admin/login.php">Админ</a>
            <?php endif; ?>
            <?php if ($userLoggedIn): ?>
                <span class="nav-user"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></span>
                <a href="/logout-user.php">Выйти</a>
            <?php elseif ($guestBrowse): ?>
                <a href="/welcome.php">Войти</a>
            <?php else: ?>
                <a href="/welcome.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="shell site-main">
<?php else: ?>
<main class="auth-main">
<?php endif; ?>

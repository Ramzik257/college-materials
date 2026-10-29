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
$userAvatar = $userLoggedIn ? currentUserAvatar() : '';
?>
<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($pageDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="color-scheme" content="dark light">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <script>
      (function () {
        try {
          var t = localStorage.getItem('campus-notes-theme');
          if (t !== 'light' && t !== 'dark') {
            t = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
          }
          document.documentElement.setAttribute('data-theme', t);
        } catch (e) {}
      })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="<?= $isAuthPage ? 'page-auth' : 'page-app' ?>">
<?php if ($isAuthPage): ?>
<button type="button" class="theme-toggle theme-toggle-float" data-theme-toggle aria-label="Переключить тему">
    <span class="label-dark">Светлая</span>
    <span class="label-light">Тёмная</span>
</button>
<?php endif; ?>
<?php if (!$isAuthPage): ?>
<header class="site-header">
    <div class="shell header-row">
        <a class="logo" href="/index.php">Campus&nbsp;Notes</a>
        <button
            type="button"
            class="nav-toggle"
            id="nav-toggle"
            aria-controls="site-nav"
            aria-expanded="false"
            aria-label="Открыть меню"
        >
            <span class="nav-toggle-bar" aria-hidden="true"></span>
            <span class="nav-toggle-bar" aria-hidden="true"></span>
            <span class="nav-toggle-bar" aria-hidden="true"></span>
        </button>
        <nav class="site-nav" id="site-nav" aria-label="Основная">
            <a href="/index.php">Каталог</a>
            <?php if ($adminLoggedIn): ?>
                <a href="/admin/index.php">Панель</a>
                <a href="/admin/logout.php">Выйти (админ)</a>
            <?php else: ?>
                <a class="nav-quiet" href="/admin/login.php">Админ</a>
            <?php endif; ?>
            <?php if ($userLoggedIn): ?>
                <a class="nav-profile" href="/profile.php" title="Мой профиль">
                    <?php if ($userAvatar !== ''): ?>
                        <img class="nav-avatar" src="<?= htmlspecialchars($userAvatar, ENT_QUOTES, 'UTF-8') ?>" alt="">
                    <?php endif; ?>
                    <span class="nav-profile-name"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></span>
                </a>
                <a href="/logout-user.php">Выйти</a>
            <?php elseif ($guestBrowse): ?>
                <a href="/welcome.php">Войти</a>
            <?php else: ?>
                <a href="/welcome.php">Регистрация</a>
            <?php endif; ?>
            <button type="button" class="theme-toggle" data-theme-toggle aria-label="Переключить тему">
                <span class="label-dark">Светлая</span>
                <span class="label-light">Тёмная</span>
            </button>
        </nav>
    </div>
</header>
<main class="shell site-main">
<?php else: ?>
<main class="auth-main">
<?php endif; ?>

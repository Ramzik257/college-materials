<?php
/**
 * Стартовая страница: регистрация / вход.
 * Каталог открывается после входа или режима гостя.
 */
require_once __DIR__ . '/includes/user_auth.php';

startUserSession();

if (isUserLoggedIn()) {
    header('Location: /index.php');
    exit;
}

$mode = $_GET['mode'] ?? 'register';
if (!in_array($mode, ['register', 'login'], true)) {
    $mode = 'register';
}

$errors = [];
$nameValue = '';
$emailValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'register';

    if ($action === 'login') {
        $mode = 'login';
        $emailValue = trim($_POST['email'] ?? '');
        $result = attemptUserLogin($emailValue, $_POST['password'] ?? '');
        if ($result['ok']) {
            header('Location: /index.php');
            exit;
        }
        $errors = $result['errors'];
    } else {
        $mode = 'register';
        $nameValue = trim($_POST['name'] ?? '');
        $emailValue = trim($_POST['email'] ?? '');
        $result = registerUser($nameValue, $emailValue, $_POST['password'] ?? '');
        if ($result['ok']) {
            header('Location: /index.php');
            exit;
        }
        $errors = $result['errors'];
    }
}

$pageTitle = $mode === 'login' ? 'Вход' : 'Регистрация';
$pageDescription = 'Войдите или создайте аккаунт, чтобы открыть каталог учебных материалов';
$isAuthPage = true;
require __DIR__ . '/includes/header.php';
?>

<section class="auth-stage">
    <div class="auth-brand">
        <p class="eyebrow">Колледж · практика</p>
        <h1 class="brand-title">Campus&nbsp;Notes</h1>
        <p class="lede">Учебные материалы студентов — лекции, практика и полезные ссылки в одном месте.</p>
        <a class="ghost-link" href="/guest.php">Смотреть каталог без регистрации</a>
    </div>

    <div class="auth-panel">
        <div class="auth-tabs" role="tablist">
            <a class="auth-tab <?= $mode === 'register' ? 'is-active' : '' ?>" href="/welcome.php?mode=register">Регистрация</a>
            <a class="auth-tab <?= $mode === 'login' ? 'is-active' : '' ?>" href="/welcome.php?mode=login">Вход</a>
        </div>

        <?php if ($errors): ?>
            <ul class="form-errors" role="alert">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($mode === 'login'): ?>
            <form method="post" action="/welcome.php?mode=login" class="auth-form" data-validate="user-login" autocomplete="on">
                <input type="hidden" name="action" value="login">
                <div id="js-errors" class="form-errors" hidden></div>
                <label>
                    Email
                    <input type="email" name="email" maxlength="190" value="<?= htmlspecialchars($emailValue, ENT_QUOTES, 'UTF-8') ?>">
                </label>
                <label>
                    Пароль
                    <input type="password" name="password" maxlength="100">
                </label>
                <button type="submit" class="btn-primary">Войти</button>
            </form>
        <?php else: ?>
            <form method="post" action="/welcome.php?mode=register" class="auth-form" data-validate="user-register" autocomplete="on">
                <input type="hidden" name="action" value="register">
                <div id="js-errors" class="form-errors" hidden></div>
                <label>
                    Имя
                    <input type="text" name="name" maxlength="100" value="<?= htmlspecialchars($nameValue, ENT_QUOTES, 'UTF-8') ?>">
                </label>
                <label>
                    Email
                    <input type="email" name="email" maxlength="190" value="<?= htmlspecialchars($emailValue, ENT_QUOTES, 'UTF-8') ?>">
                </label>
                <label>
                    Пароль
                    <input type="password" name="password" maxlength="100" placeholder="минимум 6 символов">
                </label>
                <button type="submit" class="btn-primary">Создать аккаунт</button>
            </form>
        <?php endif; ?>

        <p class="auth-footnote">Админам: <a href="/admin/login.php">вход в панель</a></p>
    </div>
</section>

<script src="/assets/js/validation.js" defer></script>
<?php require __DIR__ . '/includes/footer.php'; ?>

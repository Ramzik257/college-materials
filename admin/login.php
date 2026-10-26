<?php
/**
 * Вход администратора: форма, проверка логина/пароля, сессия.
 */
require_once __DIR__ . '/../includes/auth.php';

startSession();

// Уже вошёл — в панель
if (isAdminLoggedIn()) {
    header('Location: /admin/index.php');
    exit;
}

$error = '';
$loginValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginValue = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($loginValue === '' || $password === '') {
        $error = 'Введите логин и пароль.';
    } elseif (attemptLogin($loginValue, $password)) {
        header('Location: /admin/index.php');
        exit;
    } else {
        $error = 'Неверный логин или пароль.';
    }
}

$pageTitle = 'Вход администратора';
$pageDescription = 'Страница входа в панель администратора';
require __DIR__ . '/../includes/header.php';
?>

<h1>Вход администратора</h1>
<p class="muted">Доступ только для учётной записи администратора.</p>

<?php if ($error !== ''): ?>
    <p class="error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<form method="post" action="/admin/login.php" autocomplete="off">
    <label>
        Логин
        <input type="text" name="login" required maxlength="50"
               value="<?= htmlspecialchars($loginValue, ENT_QUOTES, 'UTF-8') ?>">
    </label>
    <label>
        Пароль
        <input type="password" name="password" required maxlength="100">
    </label>
    <button type="submit">Войти</button>
</form>

<p><a href="/index.php">Вернуться в каталог</a></p>

<?php require __DIR__ . '/../includes/footer.php'; ?>

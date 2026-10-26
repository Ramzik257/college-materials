<?php
/**
 * Сессия и проверка прав администратора.
 */

require_once __DIR__ . '/db.php';

function startSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isAdminLoggedIn(): bool
{
    startSession();
    return !empty($_SESSION['admin_id']) && !empty($_SESSION['admin_login']);
}

/**
 * Редирект гостей на страницу входа.
 */
function requireAdmin(): void
{
    if (!isAdminLoggedIn()) {
        header('Location: /admin/login.php');
        exit;
    }
}

/**
 * Проверка логина и пароля. Возвращает true при успехе.
 */
function attemptLogin(string $login, string $password): bool
{
    $login = trim($login);
    if ($login === '' || $password === '') {
        return false;
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, login, password_hash FROM admins WHERE login = :login LIMIT 1');
    $stmt->execute(['login' => $login]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password_hash'])) {
        return false;
    }

    startSession();
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $admin['id'];
    $_SESSION['admin_login'] = $admin['login'];

    return true;
}

function logoutAdmin(): void
{
    startSession();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

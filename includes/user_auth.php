<?php
/**
 * Регистрация и вход обычных пользователей (не админов).
 */

require_once __DIR__ . '/db.php';

function startUserSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isUserLoggedIn(): bool
{
    startUserSession();
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_name']);
}

function isGuestBrowse(): bool
{
    startUserSession();
    return !empty($_SESSION['guest_browse']);
}

function enableGuestBrowse(): void
{
    startUserSession();
    $_SESSION['guest_browse'] = true;
}

function currentUserName(): string
{
    startUserSession();
    return (string) ($_SESSION['user_name'] ?? '');
}

/**
 * Пускает в каталог только после входа или режима гостя.
 */
function requireUserOrGuest(): void
{
    if (isUserLoggedIn() || isGuestBrowse()) {
        return;
    }
    header('Location: /welcome.php');
    exit;
}

function registerUser(string $name, string $email, string $password): array
{
    $errors = [];
    $name = trim($name);
    $email = trim(mb_strtolower($email));

    if ($name === '' || mb_strlen($name) < 2) {
        $errors[] = 'Укажите имя (минимум 2 символа).';
    } elseif (mb_strlen($name) > 100) {
        $errors[] = 'Имя не длиннее 100 символов.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Укажите корректный email.';
    } elseif (mb_strlen($email) > 190) {
        $errors[] = 'Email слишком длинный.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Пароль не короче 6 символов.';
    }

    if ($errors) {
        return ['ok' => false, 'errors' => $errors];
    }

    $pdo = db();
    $exists = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $exists->execute(['email' => $email]);
    if ($exists->fetch()) {
        return ['ok' => false, 'errors' => ['Такой email уже зарегистрирован.']];
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)');
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password_hash' => $hash,
    ]);

    startUserSession();
    session_regenerate_id(true);
    unset($_SESSION['guest_browse']);
    $_SESSION['user_id'] = (int) $pdo->lastInsertId();
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    return ['ok' => true, 'errors' => []];
}

function attemptUserLogin(string $email, string $password): array
{
    $errors = [];
    $email = trim(mb_strtolower($email));

    if ($email === '' || $password === '') {
        return ['ok' => false, 'errors' => ['Введите email и пароль.']];
    }

    $pdo = db();
    $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['ok' => false, 'errors' => ['Неверный email или пароль.']];
    }

    startUserSession();
    session_regenerate_id(true);
    unset($_SESSION['guest_browse']);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];

    return ['ok' => true, 'errors' => []];
}

function logoutUser(): void
{
    startUserSession();
    unset($_SESSION['user_id'], $_SESSION['user_name'], $_SESSION['user_email'], $_SESSION['guest_browse']);
}

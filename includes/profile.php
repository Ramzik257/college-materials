<?php
/**
 * Профиль: данные пользователя, аватар, смена пароля, избранное.
 */

require_once __DIR__ . '/user_auth.php';

function requireUser(): void
{
    if (!isUserLoggedIn()) {
        header('Location: /welcome.php?mode=login');
        exit;
    }
}

function findUserById(int $id): ?array
{
    if ($id <= 0) {
        return null;
    }

    $stmt = db()->prepare('
        SELECT id, name, email, avatar_path, bio, created_at
        FROM users
        WHERE id = :id
        LIMIT 1
    ');
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function refreshUserSession(array $user): void
{
    startUserSession();
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_avatar'] = (string) ($user['avatar_path'] ?? '');
}

function updateProfile(int $userId, string $name, string $bio): array
{
    $errors = [];
    $name = trim($name);
    $bio = trim($bio);

    if ($name === '' || mb_strlen($name) < 2) {
        $errors[] = 'Укажите имя (минимум 2 символа).';
    } elseif (mb_strlen($name) > 100) {
        $errors[] = 'Имя не длиннее 100 символов.';
    }

    if (mb_strlen($bio) > 300) {
        $errors[] = 'О себе — не длиннее 300 символов.';
    }

    if ($errors) {
        return ['ok' => false, 'errors' => $errors];
    }

    $stmt = db()->prepare('UPDATE users SET name = :name, bio = :bio WHERE id = :id');
    $stmt->execute([
        'name' => $name,
        'bio' => $bio === '' ? null : $bio,
        'id' => $userId,
    ]);

    $user = findUserById($userId);
    if ($user) {
        refreshUserSession($user);
    }

    return ['ok' => true, 'errors' => []];
}

function changeUserPassword(int $userId, string $current, string $newPassword, string $confirm): array
{
    $errors = [];

    if ($current === '' || $newPassword === '') {
        return ['ok' => false, 'errors' => ['Заполните все поля пароля.']];
    }

    if (strlen($newPassword) < 6) {
        $errors[] = 'Новый пароль не короче 6 символов.';
    }

    if ($newPassword !== $confirm) {
        $errors[] = 'Подтверждение пароля не совпадает.';
    }

    if ($errors) {
        return ['ok' => false, 'errors' => $errors];
    }

    $stmt = db()->prepare('SELECT password_hash FROM users WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $userId]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($current, $row['password_hash'])) {
        return ['ok' => false, 'errors' => ['Текущий пароль неверный.']];
    }

    $hash = password_hash($newPassword, PASSWORD_DEFAULT);
    $update = db()->prepare('UPDATE users SET password_hash = :hash WHERE id = :id');
    $update->execute(['hash' => $hash, 'id' => $userId]);

    return ['ok' => true, 'errors' => []];
}

function avatarUploadDir(): string
{
    return dirname(__DIR__) . '/uploads/avatars';
}

function saveUserAvatar(int $userId, array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'errors' => ['Выберите файл изображения.']];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'errors' => ['Не удалось загрузить файл.']];
    }

    if (($file['size'] ?? 0) > 2 * 1024 * 1024) {
        return ['ok' => false, 'errors' => ['Аватар не больше 2 МБ.']];
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $map = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($map[$mime])) {
        return ['ok' => false, 'errors' => ['Допустимы JPG, PNG или WebP.']];
    }

    $dir = avatarUploadDir();
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return ['ok' => false, 'errors' => ['Не удалось создать папку для аватаров.']];
    }

    $user = findUserById($userId);
    if ($user && !empty($user['avatar_path'])) {
        $old = dirname(__DIR__) . $user['avatar_path'];
        if (is_file($old)) {
            @unlink($old);
        }
    }

    $relative = '/uploads/avatars/user_' . $userId . '.' . $map[$mime];
    $absolute = dirname(__DIR__) . $relative;

    if (!move_uploaded_file($file['tmp_name'], $absolute)) {
        return ['ok' => false, 'errors' => ['Не удалось сохранить аватар.']];
    }

    $stmt = db()->prepare('UPDATE users SET avatar_path = :path WHERE id = :id');
    $stmt->execute(['path' => $relative, 'id' => $userId]);

    $fresh = findUserById($userId);
    if ($fresh) {
        refreshUserSession($fresh);
    }

    return ['ok' => true, 'errors' => []];
}

function removeUserAvatar(int $userId): array
{
    $user = findUserById($userId);
    if (!$user || empty($user['avatar_path'])) {
        return ['ok' => true, 'errors' => []];
    }

    $absolute = dirname(__DIR__) . $user['avatar_path'];
    if (is_file($absolute)) {
        @unlink($absolute);
    }

    $stmt = db()->prepare('UPDATE users SET avatar_path = NULL WHERE id = :id');
    $stmt->execute(['id' => $userId]);

    $fresh = findUserById($userId);
    if ($fresh) {
        refreshUserSession($fresh);
    }

    return ['ok' => true, 'errors' => []];
}

function isFavorite(int $userId, int $materialId): bool
{
    if ($userId <= 0 || $materialId <= 0) {
        return false;
    }

    $stmt = db()->prepare('
        SELECT 1 FROM favorites
        WHERE user_id = :user_id AND material_id = :material_id
        LIMIT 1
    ');
    $stmt->execute([
        'user_id' => $userId,
        'material_id' => $materialId,
    ]);

    return (bool) $stmt->fetchColumn();
}

function toggleFavorite(int $userId, int $materialId): array
{
    if ($userId <= 0 || $materialId <= 0) {
        return ['ok' => false, 'favorited' => false, 'errors' => ['Некорректные данные.']];
    }

    $check = db()->prepare('SELECT id FROM materials WHERE id = :id LIMIT 1');
    $check->execute(['id' => $materialId]);
    if (!$check->fetch()) {
        return ['ok' => false, 'favorited' => false, 'errors' => ['Материал не найден.']];
    }

    if (isFavorite($userId, $materialId)) {
        $del = db()->prepare('DELETE FROM favorites WHERE user_id = :user_id AND material_id = :material_id');
        $del->execute(['user_id' => $userId, 'material_id' => $materialId]);
        return ['ok' => true, 'favorited' => false, 'errors' => []];
    }

    $ins = db()->prepare('INSERT INTO favorites (user_id, material_id) VALUES (:user_id, :material_id)');
    $ins->execute(['user_id' => $userId, 'material_id' => $materialId]);

    return ['ok' => true, 'favorited' => true, 'errors' => []];
}

function listUserFavorites(int $userId): array
{
    $stmt = db()->prepare('
        SELECT
            m.id,
            m.title,
            m.description,
            c.name AS category_name,
            f.created_at AS favorited_at
        FROM favorites f
        JOIN materials m ON m.id = f.material_id
        JOIN categories c ON c.id = m.category_id
        WHERE f.user_id = :user_id
        ORDER BY f.created_at DESC
    ');
    $stmt->execute(['user_id' => $userId]);

    return $stmt->fetchAll();
}

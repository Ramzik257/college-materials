<?php
/**
 * Серверная проверка данных форм (повторная после JavaScript).
 */

function validateMaterialInput(array $data, PDO $pdo): array
{
    $errors = [];
    $title = trim((string) ($data['title'] ?? ''));
    $description = trim((string) ($data['description'] ?? ''));
    $categoryId = (int) ($data['category_id'] ?? 0);
    $url = trim((string) ($data['url'] ?? ''));

    if ($title === '') {
        $errors[] = 'Укажите название.';
    } elseif (mb_strlen($title) > 200) {
        $errors[] = 'Название не длиннее 200 символов.';
    }

    if ($description === '') {
        $errors[] = 'Укажите описание.';
    }

    if ($categoryId <= 0) {
        $errors[] = 'Выберите категорию.';
    } else {
        $check = $pdo->prepare('SELECT id FROM categories WHERE id = :id');
        $check->execute(['id' => $categoryId]);
        if (!$check->fetch()) {
            $errors[] = 'Выбранная категория не существует.';
        }
    }

    if ($url === '') {
        $errors[] = 'Укажите ссылку на ресурс.';
    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
        $errors[] = 'Ссылка должна быть корректным URL (например https://…).';
    } elseif (mb_strlen($url) > 500) {
        $errors[] = 'Ссылка не длиннее 500 символов.';
    } elseif (!preg_match('#^https?://#i', $url)) {
        $errors[] = 'Ссылка должна начинаться с http:// или https://.';
    }

    return [
        'errors' => $errors,
        'data' => [
            'title' => $title,
            'description' => $description,
            'category_id' => $categoryId,
            'url' => $url,
        ],
    ];
}

function validateLoginInput(string $login, string $password): array
{
    $errors = [];
    $login = trim($login);

    if ($login === '') {
        $errors[] = 'Введите логин.';
    } elseif (mb_strlen($login) > 50) {
        $errors[] = 'Логин не длиннее 50 символов.';
    }

    if ($password === '') {
        $errors[] = 'Введите пароль.';
    }

    return [
        'errors' => $errors,
        'login' => $login,
    ];
}

<?php
/**
 * Добавление и изменение материала (только для администратора).
 */
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();
$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$isEdit = $id > 0;

$material = [
    'title' => '',
    'description' => '',
    'category_id' => 0,
    'url' => '',
];
$errors = [];

if ($isEdit && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $pdo->prepare('SELECT id, title, description, category_id, url FROM materials WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    if (!$row) {
        http_response_code(404);
        exit('Материал не найден.');
    }
    $material = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $material['title'] = trim($_POST['title'] ?? '');
    $material['description'] = trim($_POST['description'] ?? '');
    $material['category_id'] = (int) ($_POST['category_id'] ?? 0);
    $material['url'] = trim($_POST['url'] ?? '');

    if ($material['title'] === '') {
        $errors[] = 'Укажите название.';
    } elseif (mb_strlen($material['title']) > 200) {
        $errors[] = 'Название не длиннее 200 символов.';
    }

    if ($material['description'] === '') {
        $errors[] = 'Укажите описание.';
    }

    if ($material['category_id'] <= 0) {
        $errors[] = 'Выберите категорию.';
    } else {
        $check = $pdo->prepare('SELECT id FROM categories WHERE id = :id');
        $check->execute(['id' => $material['category_id']]);
        if (!$check->fetch()) {
            $errors[] = 'Выбранная категория не существует.';
        }
    }

    if ($material['url'] === '') {
        $errors[] = 'Укажите ссылку на ресурс.';
    } elseif (!filter_var($material['url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'Ссылка должна быть корректным URL (например https://…).';
    } elseif (mb_strlen($material['url']) > 500) {
        $errors[] = 'Ссылка не длиннее 500 символов.';
    }

    if (!$errors) {
        if ($isEdit) {
            $stmt = $pdo->prepare('
                UPDATE materials
                SET title = :title,
                    description = :description,
                    category_id = :category_id,
                    url = :url
                WHERE id = :id
            ');
            $stmt->execute([
                'title' => $material['title'],
                'description' => $material['description'],
                'category_id' => $material['category_id'],
                'url' => $material['url'],
                'id' => $id,
            ]);
        } else {
            $stmt = $pdo->prepare('
                INSERT INTO materials (title, description, category_id, url)
                VALUES (:title, :description, :category_id, :url)
            ');
            $stmt->execute([
                'title' => $material['title'],
                'description' => $material['description'],
                'category_id' => $material['category_id'],
                'url' => $material['url'],
            ]);
        }

        header('Location: /admin/index.php');
        exit;
    }
}

$pageTitle = $isEdit ? 'Изменить материал' : 'Добавить материал';
$pageDescription = 'Форма создания и редактирования учебного материала';
require __DIR__ . '/../includes/header.php';
?>

<h1><?= $isEdit ? 'Изменить материал' : 'Добавить материал' ?></h1>
<p><a href="/admin/index.php">← К списку</a></p>

<?php if ($errors): ?>
    <ul class="error" role="alert">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form method="post" action="/admin/material-form.php<?= $isEdit ? '?id=' . (int) $id : '' ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int) $id ?>">
    <?php endif; ?>

    <label>
        Название
        <input type="text" name="title" required maxlength="200"
               value="<?= htmlspecialchars($material['title'], ENT_QUOTES, 'UTF-8') ?>">
    </label>

    <label>
        Описание
        <textarea name="description" required rows="5"><?= htmlspecialchars($material['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
    </label>

    <label>
        Категория
        <select name="category_id" required>
            <option value="0">Выберите категорию</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>"
                    <?= (int) $material['category_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Ссылка на внешний ресурс
        <input type="url" name="url" required maxlength="500" placeholder="https://…"
               value="<?= htmlspecialchars($material['url'], ENT_QUOTES, 'UTF-8') ?>">
    </label>

    <button type="submit"><?= $isEdit ? 'Сохранить' : 'Добавить' ?></button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>

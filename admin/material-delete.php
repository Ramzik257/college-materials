<?php
/**
 * Удаление материала (только для администратора, через POST).
 */
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    exit('Не указан материал.');
}

$stmt = $pdo->prepare('
    SELECT m.id, m.title, c.name AS category_name
    FROM materials m
    JOIN categories c ON c.id = m.category_id
    WHERE m.id = :id
');
$stmt->execute(['id' => $id]);
$material = $stmt->fetch();

if (!$material) {
    http_response_code(404);
    exit('Материал не найден.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete = $pdo->prepare('DELETE FROM materials WHERE id = :id');
    $delete->execute(['id' => $id]);
    header('Location: /admin/index.php');
    exit;
}

$pageTitle = 'Удалить материал';
$pageDescription = 'Подтверждение удаления учебного материала';
require __DIR__ . '/../includes/header.php';
?>

<h1>Удалить материал?</h1>
<p>
    Материал
    <strong><?= htmlspecialchars($material['title'], ENT_QUOTES, 'UTF-8') ?></strong>
    (<?= htmlspecialchars($material['category_name'], ENT_QUOTES, 'UTF-8') ?>)
    будет удалён без возможности восстановления.
</p>

<form method="post" action="/admin/material-delete.php?id=<?= (int) $id ?>">
    <input type="hidden" name="id" value="<?= (int) $id ?>">
    <button type="submit" class="contrast">Да, удалить</button>
    <a href="/admin/index.php" role="button" class="secondary">Отмена</a>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>

<?php
/**
 * Список материалов в панели администратора.
 */
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();
$materials = $pdo->query('
    SELECT
        m.id,
        m.title,
        m.url,
        c.name AS category_name
    FROM materials m
    JOIN categories c ON c.id = m.category_id
    ORDER BY m.created_at DESC
')->fetchAll();

$pageTitle = 'Панель администратора';
$pageDescription = 'Управление учебными материалами';
require __DIR__ . '/../includes/header.php';
?>

<h1>Панель администратора</h1>
<p>
    Вы вошли как
    <strong><?= htmlspecialchars($_SESSION['admin_login'], ENT_QUOTES, 'UTF-8') ?></strong>.
    <a href="/admin/logout.php">Выйти</a>
</p>

<p><a href="/admin/material-form.php"><strong>+ Добавить материал</strong></a></p>

<?php if (!$materials): ?>
    <p class="muted">Материалов пока нет.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Название</th>
                <th>Категория</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($materials as $material): ?>
                <tr>
                    <td>
                        <a href="/material.php?id=<?= (int) $material['id'] ?>">
                            <?= htmlspecialchars($material['title'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($material['category_name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a href="/admin/material-form.php?id=<?= (int) $material['id'] ?>">Изменить</a>
                        ·
                        <a href="/admin/material-delete.php?id=<?= (int) $material['id'] ?>">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<p><a href="/index.php">← К каталогу</a></p>

<?php require __DIR__ . '/../includes/footer.php'; ?>

<?php
/**
 * Панель администратора (защищена сессией).
 * CRUD материалов добавится в отдельной задаче.
 */
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'Панель администратора';
$pageDescription = 'Управление учебными материалами';
require __DIR__ . '/../includes/header.php';
?>

<h1>Панель администратора</h1>
<p>
    Вы вошли как
    <strong><?= htmlspecialchars($_SESSION['admin_login'], ENT_QUOTES, 'UTF-8') ?></strong>.
</p>
<p class="muted">
    Список материалов и CRUD появятся в следующей задаче.
    Пока можно проверить вход и выход.
</p>
<p>
    <a href="/index.php">К каталогу</a>
    ·
    <a href="/admin/logout.php">Выйти</a>
</p>

<?php require __DIR__ . '/../includes/footer.php'; ?>

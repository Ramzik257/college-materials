<?php
/**
 * Страница одного материала.
 */
require_once __DIR__ . '/includes/user_auth.php';
require_once __DIR__ . '/includes/db.php';

requireUserOrGuest();

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(404);
    exit('Материал не найден.');
}

$pdo = db();
$stmt = $pdo->prepare('
    SELECT
        m.id,
        m.title,
        m.description,
        m.url,
        m.created_at,
        c.name AS category_name
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

$pageTitle = $material['title'];
$pageDescription = mb_strimwidth($material['description'], 0, 150, '…', 'UTF-8');
require __DIR__ . '/includes/header.php';
?>

<a class="back-link" href="/index.php">← К каталогу</a>

<article class="detail-card">
    <h1><?= htmlspecialchars($material['title'], ENT_QUOTES, 'UTF-8') ?></h1>
    <span class="chip"><?= htmlspecialchars($material['category_name'], ENT_QUOTES, 'UTF-8') ?></span>
    <p class="material-body"><?= nl2br(htmlspecialchars($material['description'], ENT_QUOTES, 'UTF-8')) ?></p>
    <p class="detail-actions">
        <a class="button-link" href="<?= htmlspecialchars($material['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
            Открыть внешний ресурс
        </a>
    </p>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>

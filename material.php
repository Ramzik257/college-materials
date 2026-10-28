<?php
/**
 * Страница одного материала.
 */
require_once __DIR__ . '/includes/profile.php';
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

$loggedIn = isUserLoggedIn();
$favorited = $loggedIn ? isFavorite(currentUserId(), (int) $material['id']) : false;
$favNotice = $_GET['fav'] ?? null;

$pageTitle = $material['title'];
$pageDescription = mb_strimwidth($material['description'], 0, 150, '…', 'UTF-8');
require __DIR__ . '/includes/header.php';
?>

<a class="back-link" href="/index.php">← К каталогу</a>

<?php if ($favNotice === '1'): ?>
    <p class="flash-ok" role="status">Добавлено в избранное. Смотрите в <a href="/profile.php?tab=favorites">профиле</a>.</p>
<?php elseif ($favNotice === '0'): ?>
    <p class="flash-ok" role="status">Убрано из избранного.</p>
<?php endif; ?>

<article class="detail-card">
    <h1><?= htmlspecialchars($material['title'], ENT_QUOTES, 'UTF-8') ?></h1>
    <span class="chip"><?= htmlspecialchars($material['category_name'], ENT_QUOTES, 'UTF-8') ?></span>
    <p class="material-body"><?= nl2br(htmlspecialchars($material['description'], ENT_QUOTES, 'UTF-8')) ?></p>
    <div class="detail-actions">
        <a class="button-link" href="<?= htmlspecialchars($material['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
            Открыть внешний ресурс
        </a>
        <?php if ($loggedIn): ?>
            <form method="post" action="/favorite-toggle.php" class="inline-form">
                <input type="hidden" name="material_id" value="<?= (int) $material['id'] ?>">
                <button type="submit" class="<?= $favorited ? 'btn-secondary' : '' ?>">
                    <?= $favorited ? 'Убрать из избранного' : 'В избранное' ?>
                </button>
            </form>
        <?php else: ?>
            <p class="muted fav-hint"><a href="/welcome.php?mode=login">Войдите</a>, чтобы сохранить в избранное.</p>
        <?php endif; ?>
    </div>
</article>

<?php require __DIR__ . '/includes/footer.php'; ?>

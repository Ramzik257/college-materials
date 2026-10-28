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
        m.category_id,
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

$relatedStmt = $pdo->prepare('
    SELECT m.id, m.title, c.name AS category_name
    FROM materials m
    JOIN categories c ON c.id = m.category_id
    WHERE m.category_id = :category_id AND m.id <> :id
    ORDER BY m.created_at DESC
    LIMIT 3
');
$relatedStmt->execute([
    'category_id' => (int) $material['category_id'],
    'id' => (int) $material['id'],
]);
$related = $relatedStmt->fetchAll();

$loggedIn = isUserLoggedIn();
$favorited = $loggedIn ? isFavorite(currentUserId(), (int) $material['id']) : false;
$favNotice = $_GET['fav'] ?? null;

$urlHost = parse_url($material['url'], PHP_URL_HOST) ?: 'внешний сайт';
$urlHost = preg_replace('/^www\./i', '', (string) $urlHost);
$addedAt = date('d.m.Y', strtotime($material['created_at']));
$wordCount = preg_match_all('/\p{L}+/u', $material['description'], $matches) ? count($matches[0]) : 0;
$readMinutes = max(1, (int) ceil($wordCount / 120));

$categoryTips = [
    'Лекции' => [
        'title' => 'Как читать лекцию',
        'items' => [
            'Сначала пробегитесь по заголовкам, потом углубляйтесь.',
            'Выпишите 3–5 терминов, которые встретите впервые.',
            'После материала ответьте себе: «что я могу объяснить другу?»',
        ],
    ],
    'Практика' => [
        'title' => 'Как пройти практику',
        'items' => [
            'Откройте ресурс и сразу повторяйте шаги у себя.',
            'Не копируйте код слепо — меняйте имена и проверяйте результат.',
            'Если застряли, вернитесь к связанным материалам ниже.',
        ],
    ],
    'Полезное' => [
        'title' => 'Как пользоваться',
        'items' => [
            'Сохраните ссылку в избранное, если ещё будете возвращаться.',
            'Используйте как шпаргалку рядом с учебной задачей.',
            'Поделитесь материалом с одногруппником — так лучше запоминается.',
        ],
    ],
];
$tip = $categoryTips[$material['category_name']] ?? [
    'title' => 'Подсказка',
    'items' => [
        'Откройте внешний ресурс и сверьтесь с описанием на этой странице.',
        'Добавьте материал в избранное, чтобы быстро найти позже.',
    ],
];

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

<section class="detail-layout">
    <article class="detail-card detail-main">
        <header class="detail-header">
            <span class="chip"><?= htmlspecialchars($material['category_name'], ENT_QUOTES, 'UTF-8') ?></span>
            <h1><?= htmlspecialchars($material['title'], ENT_QUOTES, 'UTF-8') ?></h1>
            <ul class="meta-row" aria-label="Краткая информация">
                <li><span class="meta-label">Источник</span> <?= htmlspecialchars($urlHost, ENT_QUOTES, 'UTF-8') ?></li>
                <li><span class="meta-label">Добавлен</span> <?= htmlspecialchars($addedAt, ENT_QUOTES, 'UTF-8') ?></li>
                <li><span class="meta-label">Чтение</span> ~<?= (int) $readMinutes ?> мин</li>
            </ul>
        </header>

        <div class="info-block">
            <h2 class="info-title">О материале</h2>
            <p class="material-body"><?= nl2br(htmlspecialchars($material['description'], ENT_QUOTES, 'UTF-8')) ?></p>
        </div>

        <div class="info-block resource-block">
            <h2 class="info-title">Внешний ресурс</h2>
            <p class="muted">Материал ведёт на <?= htmlspecialchars($urlHost, ENT_QUOTES, 'UTF-8') ?>. Откроется в новой вкладке.</p>
            <p class="resource-url"><?= htmlspecialchars($material['url'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>

        <div class="detail-actions">
            <a class="button-link" href="<?= htmlspecialchars($material['url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer">
                Открыть ресурс
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

    <aside class="detail-side">
        <div class="side-card tip-card">
            <h2 class="info-title"><?= htmlspecialchars($tip['title'], ENT_QUOTES, 'UTF-8') ?></h2>
            <ol class="tip-list">
                <?php foreach ($tip['items'] as $item): ?>
                    <li><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ol>
        </div>

        <div class="side-card">
            <h2 class="info-title">Карточка</h2>
            <dl class="fact-list">
                <div>
                    <dt>Категория</dt>
                    <dd><?= htmlspecialchars($material['category_name'], ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt>Дата в каталоге</dt>
                    <dd><?= htmlspecialchars($addedAt, ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt>Сайт</dt>
                    <dd><?= htmlspecialchars($urlHost, ENT_QUOTES, 'UTF-8') ?></dd>
                </div>
                <div>
                    <dt>В избранном</dt>
                    <dd><?= $favorited ? 'да' : ($loggedIn ? 'нет' : 'нужен вход') ?></dd>
                </div>
            </dl>
        </div>

        <?php if ($related): ?>
            <div class="side-card">
                <h2 class="info-title">Похожие в «<?= htmlspecialchars($material['category_name'], ENT_QUOTES, 'UTF-8') ?>»</h2>
                <ul class="related-list">
                    <?php foreach ($related as $item): ?>
                        <li>
                            <a href="/material.php?id=<?= (int) $item['id'] ?>">
                                <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </aside>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>

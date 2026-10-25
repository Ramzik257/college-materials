<?php
/**
 * Главная: список материалов, поиск по названию, фильтр по категории.
 */
require_once __DIR__ . '/includes/db.php';

$pdo = db();

$search = trim($_GET['q'] ?? '');
$categoryId = (int) ($_GET['category'] ?? 0);

$categories = $pdo->query('SELECT id, name FROM categories ORDER BY name')->fetchAll();

$sql = '
    SELECT
        m.id,
        m.title,
        m.description,
        m.url,
        c.name AS category_name
    FROM materials m
    JOIN categories c ON c.id = m.category_id
    WHERE 1 = 1
';
$params = [];

if ($search !== '') {
    $sql .= ' AND m.title LIKE :search';
    $params['search'] = '%' . $search . '%';
}

if ($categoryId > 0) {
    $sql .= ' AND m.category_id = :category_id';
    $params['category_id'] = $categoryId;
}

$sql .= ' ORDER BY m.created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$materials = $stmt->fetchAll();

$pageTitle = 'Каталог учебных материалов';
$pageDescription = 'Список материалов с поиском и фильтром по категориям';
require __DIR__ . '/includes/header.php';
?>

<h1>Учебные материалы</h1>
<p class="muted">Найдите материал по названию или выберите категорию.</p>

<form class="filters" method="get" action="/index.php" role="search">
    <label>
        Поиск по названию
        <input type="search" name="q" placeholder="Например: HTML" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
    </label>
    <label>
        Категория
        <select name="category">
            <option value="0">Все категории</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <button type="submit">Найти</button>
</form>

<?php if (!$materials): ?>
    <article>
        <p>Ничего не найдено. Измените запрос или сбросьте фильтр.</p>
        <p><a href="/index.php">Показать все материалы</a></p>
    </article>
<?php else: ?>
    <p class="muted">Найдено: <?= count($materials) ?></p>
    <?php foreach ($materials as $material): ?>
        <article class="material-card">
            <header>
                <h2>
                    <a href="/material.php?id=<?= (int) $material['id'] ?>">
                        <?= htmlspecialchars($material['title'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </h2>
                <p><small><?= htmlspecialchars($material['category_name'], ENT_QUOTES, 'UTF-8') ?></small></p>
            </header>
            <p><?= htmlspecialchars(mb_strimwidth($material['description'], 0, 160, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?></p>
        </article>
    <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>

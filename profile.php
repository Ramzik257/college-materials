<?php
/**
 * Личный кабинет: профиль, аватар, пароль, избранное.
 */
require_once __DIR__ . '/includes/profile.php';

requireUser();

$userId = currentUserId();
$user = findUserById($userId);
if (!$user) {
    logoutUser();
    header('Location: /welcome.php');
    exit;
}

$tab = $_GET['tab'] ?? 'profile';
if (!in_array($tab, ['profile', 'password', 'favorites'], true)) {
    $tab = 'profile';
}

$flash = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $tab = 'profile';
        $result = updateProfile($userId, $_POST['name'] ?? '', $_POST['bio'] ?? '');
        if ($result['ok']) {
            $flash = 'Профиль сохранён.';
            $user = findUserById($userId) ?? $user;
        } else {
            $errors = $result['errors'];
            $user['name'] = trim($_POST['name'] ?? $user['name']);
            $user['bio'] = trim($_POST['bio'] ?? (string) ($user['bio'] ?? ''));
        }
    } elseif ($action === 'upload_avatar') {
        $tab = 'profile';
        $result = saveUserAvatar($userId, $_FILES['avatar'] ?? []);
        if ($result['ok']) {
            $flash = 'Аватар обновлён.';
            $user = findUserById($userId) ?? $user;
        } else {
            $errors = $result['errors'];
        }
    } elseif ($action === 'remove_avatar') {
        $tab = 'profile';
        removeUserAvatar($userId);
        $flash = 'Аватар удалён.';
        $user = findUserById($userId) ?? $user;
    } elseif ($action === 'change_password') {
        $tab = 'password';
        $result = changeUserPassword(
            $userId,
            $_POST['current_password'] ?? '',
            $_POST['new_password'] ?? '',
            $_POST['confirm_password'] ?? ''
        );
        if ($result['ok']) {
            $flash = 'Пароль изменён.';
        } else {
            $errors = $result['errors'];
        }
    } elseif ($action === 'unfavorite') {
        $tab = 'favorites';
        $materialId = (int) ($_POST['material_id'] ?? 0);
        toggleFavorite($userId, $materialId);
        $flash = 'Материал убран из избранного.';
    }
}

$favorites = listUserFavorites($userId);
$avatarUrl = (string) ($user['avatar_path'] ?? '');
$initial = mb_strtoupper(mb_substr((string) $user['name'], 0, 1, 'UTF-8'), 'UTF-8');

$pageTitle = 'Профиль · Campus Notes';
$pageDescription = 'Личный кабинет: аватар, данные и избранные материалы';
require __DIR__ . '/includes/header.php';
?>

<section class="page-intro">
    <h1>Мой профиль</h1>
    <p class="muted">Аватар, данные аккаунта и избранные материалы.</p>
</section>

<?php if ($flash): ?>
    <p class="flash-ok" role="status"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></p>
<?php endif; ?>

<?php if ($errors): ?>
    <ul class="form-errors" role="alert">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="profile-layout">
    <aside class="profile-card">
        <div class="avatar-wrap">
            <?php if ($avatarUrl !== ''): ?>
                <img class="avatar-img" src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Аватар <?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>">
            <?php else: ?>
                <div class="avatar-fallback" aria-hidden="true"><?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        </div>
        <h2 class="profile-name"><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p class="muted"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php if (!empty($user['bio'])): ?>
            <p class="profile-bio"><?= nl2br(htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8')) ?></p>
        <?php else: ?>
            <p class="muted">Пока без описания — добавьте пару слов о себе.</p>
        <?php endif; ?>
        <p class="muted profile-meta">В Campus Notes с <?= htmlspecialchars(date('d.m.Y', strtotime((string) $user['created_at'])), ENT_QUOTES, 'UTF-8') ?></p>
    </aside>

    <div class="profile-main">
        <div class="auth-tabs profile-tabs" role="tablist">
            <a class="auth-tab <?= $tab === 'profile' ? 'is-active' : '' ?>" href="/profile.php?tab=profile">Профиль</a>
            <a class="auth-tab <?= $tab === 'password' ? 'is-active' : '' ?>" href="/profile.php?tab=password">Пароль</a>
            <a class="auth-tab <?= $tab === 'favorites' ? 'is-active' : '' ?>" href="/profile.php?tab=favorites">Избранное (<?= count($favorites) ?>)</a>
        </div>

        <?php if ($tab === 'profile'): ?>
            <form method="post" action="/profile.php?tab=profile" class="profile-form" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_avatar">
                <fieldset class="profile-fieldset">
                    <legend>Аватар</legend>
                    <label>
                        Файл (JPG, PNG, WebP до 2 МБ)
                        <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp">
                    </label>
                    <div class="filter-actions">
                        <button type="submit">Загрузить</button>
                        <?php if ($avatarUrl !== ''): ?>
                            <button type="submit" name="action" value="remove_avatar" class="btn-secondary" formnovalidate>Удалить</button>
                        <?php endif; ?>
                    </div>
                </fieldset>
            </form>

            <form method="post" action="/profile.php?tab=profile" class="profile-form" data-validate="profile">
                <input type="hidden" name="action" value="update_profile">
                <div id="js-errors" class="form-errors" hidden></div>
                <fieldset class="profile-fieldset">
                    <legend>Данные</legend>
                    <label>
                        Имя
                        <input type="text" name="name" maxlength="100" value="<?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?>">
                    </label>
                    <label>
                        Email
                        <input type="email" value="<?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?>" disabled>
                    </label>
                    <label>
                        О себе
                        <textarea name="bio" rows="4" maxlength="300" placeholder="Группа, курс, что ищете в каталоге…"><?= htmlspecialchars((string) ($user['bio'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                    </label>
                    <button type="submit">Сохранить</button>
                </fieldset>
            </form>

        <?php elseif ($tab === 'password'): ?>
            <form method="post" action="/profile.php?tab=password" class="profile-form" data-validate="change-password" autocomplete="off">
                <input type="hidden" name="action" value="change_password">
                <div id="js-errors" class="form-errors" hidden></div>
                <fieldset class="profile-fieldset">
                    <legend>Смена пароля</legend>
                    <label>
                        Текущий пароль
                        <input type="password" name="current_password" maxlength="100">
                    </label>
                    <label>
                        Новый пароль
                        <input type="password" name="new_password" maxlength="100" placeholder="минимум 6 символов">
                    </label>
                    <label>
                        Повтор нового пароля
                        <input type="password" name="confirm_password" maxlength="100">
                    </label>
                    <button type="submit">Сменить пароль</button>
                </fieldset>
            </form>

        <?php else: ?>
            <?php if (!$favorites): ?>
                <article class="detail-card">
                    <p>Пока пусто. Откройте материал и нажмите «В избранное».</p>
                    <p><a href="/index.php">Перейти в каталог</a></p>
                </article>
            <?php else: ?>
                <div class="material-list">
                    <?php foreach ($favorites as $item): ?>
                        <article class="material-card">
                            <h2>
                                <a href="/material.php?id=<?= (int) $item['id'] ?>">
                                    <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            </h2>
                            <span class="chip"><?= htmlspecialchars($item['category_name'], ENT_QUOTES, 'UTF-8') ?></span>
                            <p class="material-body"><?= htmlspecialchars(mb_strimwidth($item['description'], 0, 140, '…', 'UTF-8'), ENT_QUOTES, 'UTF-8') ?></p>
                            <form method="post" action="/profile.php?tab=favorites" class="inline-form">
                                <input type="hidden" name="action" value="unfavorite">
                                <input type="hidden" name="material_id" value="<?= (int) $item['id'] ?>">
                                <button type="submit" class="btn-secondary">Убрать</button>
                            </form>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script src="/assets/js/validation.js" defer></script>
<?php require __DIR__ . '/includes/footer.php'; ?>

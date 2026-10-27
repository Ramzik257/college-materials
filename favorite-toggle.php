<?php
/**
 * Добавить / убрать материал из избранного (только для вошедших пользователей).
 */
require_once __DIR__ . '/includes/profile.php';

requireUser();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$materialId = (int) ($_POST['material_id'] ?? 0);
$result = toggleFavorite(currentUserId(), $materialId);
$flag = !empty($result['favorited']) ? '1' : '0';

header('Location: /material.php?id=' . max(0, $materialId) . '&fav=' . $flag);
exit;

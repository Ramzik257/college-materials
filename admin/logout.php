<?php
/**
 * Выход администратора: очистка сессии.
 */
require_once __DIR__ . '/../includes/auth.php';

logoutAdmin();
header('Location: /admin/login.php');
exit;

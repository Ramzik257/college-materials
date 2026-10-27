<?php
/**
 * Режим гостя: каталог без регистрации (для требований ТЗ).
 */
require_once __DIR__ . '/includes/user_auth.php';

enableGuestBrowse();
header('Location: /index.php');
exit;

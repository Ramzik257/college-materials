<?php
require_once __DIR__ . '/includes/user_auth.php';

logoutUser();
header('Location: /welcome.php');
exit;

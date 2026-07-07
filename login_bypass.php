<?php
session_start();
require_once 'config/database.php';
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT id, full_name FROM users LIMIT 1");
$user = $stmt->fetch();
if ($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['full_name'] = $user['full_name'];
} else {
    $_SESSION['user_id'] = 1;
    $_SESSION['full_name'] = 'Admin Bypass';
}
header('Location: dashboard.php');
exit;

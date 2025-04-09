<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Clear all notifications for this user
$stmt = $pdo->prepare("DELETE FROM notifications WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>

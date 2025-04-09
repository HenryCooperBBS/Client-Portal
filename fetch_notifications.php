<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0, 'notifications' => []]);
    exit;
}

// Count unread
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$_SESSION['user_id']]);
$count = $stmt->fetchColumn();

// Fetch latest 5
$stmt = $pdo->prepare("SELECT id, message, is_read, DATE_FORMAT(created_at, '%b %e, %l:%i %p') as created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'count' => $count,
    'notifications' => $notifications
]);
?>

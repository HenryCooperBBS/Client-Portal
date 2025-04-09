<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch current user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

// Only allow admins
if (!$currentUser || $currentUser['is_admin'] != 1) {
    header("Location: dashboard.php");
    exit;
}

// Get the user id to promote/demote
if (isset($_GET['id'])) {
    $userId = (int)$_GET['id'];

    // Prevent admin from changing their own role
    if ($userId == $_SESSION['user_id']) {
        $_SESSION['flash_admin'] = "You cannot change your own admin status.";
    } else {
        // Get the target user
        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $targetUser = $stmt->fetch();

        if ($targetUser) {
            $newStatus = $targetUser['is_admin'] ? 0 : 1;

            $stmt = $pdo->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
            $stmt->execute([$newStatus, $userId]);

            $_SESSION['flash_admin'] = $newStatus ? "User promoted to Admin." : "User demoted to Regular user.";
        } else {
            $_SESSION['flash_admin'] = "User not found.";
        }
    }
}

header("Location: admin.php");
exit;
?>

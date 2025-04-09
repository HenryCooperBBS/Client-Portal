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

// Get user id to delete
if (isset($_GET['id'])) {
    $userId = (int)$_GET['id'];

    // Prevent admin from deleting themselves
    if ($userId == $_SESSION['user_id']) {
        $_SESSION['flash_admin'] = "You cannot delete your own account.";
    } else {
        // Delete user's uploaded files
        $stmt = $pdo->prepare("SELECT * FROM uploads WHERE user_id = ?");
        $stmt->execute([$userId]);
        $uploads = $stmt->fetchAll();

        foreach ($uploads as $upload) {
            $filePath = 'uploads/' . $upload['filename'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete user uploads from DB
        $stmt = $pdo->prepare("DELETE FROM uploads WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Delete user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        $_SESSION['flash_admin'] = "User deleted successfully.";
    }
}

header("Location: admin.php");
exit;
?>

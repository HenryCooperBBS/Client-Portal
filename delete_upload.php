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

// Get upload ID
if (isset($_GET['id'])) {
    $uploadId = (int)$_GET['id'];

    // Find the file
    $stmt = $pdo->prepare("SELECT filename FROM uploads WHERE id = ?");
    $stmt->execute([$uploadId]);
    $upload = $stmt->fetch();

    if ($upload) {
        $filePath = 'uploads/' . $upload['filename'];

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from DB
        $stmt = $pdo->prepare("DELETE FROM uploads WHERE id = ?");
        $stmt->execute([$uploadId]);

        $_SESSION['flash_admin'] = "Upload deleted successfully.";
    }
}

header("Location: view_uploads.php");
exit;
?>

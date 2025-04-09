<?php
session_start();
require_once 'includes/db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Must have an upload ID
if (!isset($_GET['id'])) {
    header('Location: feed.php');
    exit;
}

$uploadId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];

// Check if the upload exists
$stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ?");
$stmt->execute([$uploadId]);
$upload = $stmt->fetch();

if (!$upload) {
    // Upload doesn't exist
    header('Location: feed.php');
    exit;
}

// Check if user is admin
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
$isAdmin = $user && $user['is_admin'] == 1;

// Allow delete if:
// - User is the owner of the upload
// - OR user is an admin
if ($upload['user_id'] == $userId || $isAdmin) {
    // Delete the file from the server
    $filePath = 'uploads/' . $upload['filename'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM uploads WHERE id = ?");
    $stmt->execute([$uploadId]);
}

// Redirect back
header('Location: dashboard.php');
exit;
?>

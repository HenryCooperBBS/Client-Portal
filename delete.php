<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $uploadId = (int)$_GET['id'];

    // Get the file info from the database
    $stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ? AND user_id = ?");
    $stmt->execute([$uploadId, $_SESSION['user_id']]);
    $file = $stmt->fetch();

    if ($file) {
        $filePath = 'uploads/' . $file['filename'];

        // Delete the file from the server
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete the record from the database
        $stmt = $pdo->prepare("DELETE FROM uploads WHERE id = ?");
        $stmt->execute([$uploadId]);

        $_SESSION['flash_upload'] = "File deleted successfully!";
    } else {
        $_SESSION['flash_upload'] = "File not found or you don't have permission.";
    }
}

header("Location: dashboard.php");
exit;
?>

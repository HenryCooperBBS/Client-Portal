<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['upload']) && isset($_FILES['file'])) {
    $userId = $_SESSION['user_id'];
    $file = $_FILES['file'];

    if ($file['error'] === 0) {
        $filename = time() . '_' . basename($file['name']);
        $destination = 'uploads/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $stmt = $pdo->prepare("INSERT INTO uploads (user_id, filename) VALUES (?, ?)");
            $stmt->execute([$userId, $filename]);
            $_SESSION['flash_upload'] = "File uploaded successfully!";
        } else {
            $_SESSION['flash_upload'] = "Error uploading file.";
        }
    } else {
        $_SESSION['flash_upload'] = "File error.";
    }
}

header("Location: dashboard.php");
exit;
?>

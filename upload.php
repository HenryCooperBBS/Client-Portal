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
    $name = trim($_POST['name']);
    $comment = trim($_POST['comment']);
    $link = trim($_POST['link']);
    $githubLink = trim($_POST['github_link']);

    // Validation
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    if ($file['error'] === 0) {
        if (!in_array($file['type'], $allowedTypes)) {
            $_SESSION['flash_upload'] = "Only PDF, JPG, and PNG files are allowed.";
        } elseif ($file['size'] > $maxFileSize) {
            $_SESSION['flash_upload'] = "File is too large. Maximum size is 2MB.";
        } else {
            $filename = time() . '_' . basename($file['name']);
            $destination = 'uploads/' . $filename;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                $stmt = $pdo->prepare("INSERT INTO uploads (user_id, filename, name, comment, link, github_link) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$userId, $filename, $name, $comment, $link, $githubLink]);                             
                $_SESSION['flash_upload'] = "File uploaded successfully!";
            } else {
                $_SESSION['flash_upload'] = "Error uploading file.";
            }
        }
    } else {
        $_SESSION['flash_upload'] = "File upload error.";
    }
}

header("Location: dashboard.php");
exit;

?>

<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Not logged in";
    exit;
}

if (isset($_POST['upload_id'])) {
    $uploadId = (int)$_POST['upload_id'];
    $userId = $_SESSION['user_id'];

    // Check if user already liked
    $stmt = $pdo->prepare("SELECT * FROM likes WHERE user_id = ? AND upload_id = ?");
    $stmt->execute([$userId, $uploadId]);
    $existing = $stmt->fetch();

    if (!$existing) {
        // Insert like
        $stmt = $pdo->prepare("INSERT INTO likes (user_id, upload_id) VALUES (?, ?)");
        $stmt->execute([$userId, $uploadId]);
        // Find upload owner
        $stmt = $pdo->prepare("SELECT user_id FROM uploads WHERE id = ?");
        $stmt->execute([$uploadId]);
        $uploadOwner = $stmt->fetch();

        if ($uploadOwner && $uploadOwner['user_id'] != $userId) { // Don't notify yourself
            $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $liker = $stmt->fetch();

            if ($liker) {
                $message = $liker['username'] . " liked your upload.";
                $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
                $stmt->execute([$uploadOwner['user_id'], $message]);
            }
        }

    } else {
        // Unlike (toggle)
        $stmt = $pdo->prepare("DELETE FROM likes WHERE user_id = ? AND upload_id = ?");
        $stmt->execute([$userId, $uploadId]);
    }

    // Return new like count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE upload_id = ?");
    $stmt->execute([$uploadId]);
    echo $stmt->fetchColumn();
}
?>

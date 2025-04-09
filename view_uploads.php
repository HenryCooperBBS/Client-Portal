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
?>

<?php include 'templates/header.php'; ?>

<div class="flex"> <!-- Open flex for sidebar layout -->

<?php include 'includes/admin-sidebar.php'; ?>

<div class="flex-1 p-10">
    <h2 class="text-3xl font-bold mb-8 text-center">All Uploaded Files</h2>

    <?php
    $stmt = $pdo->query("SELECT uploads.id, uploads.filename, uploads.uploaded_at, users.username 
                         FROM uploads 
                         JOIN users ON uploads.user_id = users.id 
                         ORDER BY uploads.uploaded_at DESC");

    $uploads = $stmt->fetchAll();
    ?>

    <?php if ($uploads): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php foreach ($uploads as $upload): ?>
                <div class="bg-white rounded shadow p-4 flex flex-col items-center">
                    <?php
                    $ext = strtolower(pathinfo($upload['filename'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png'])):
                    ?>
                        <a href="uploads/<?php echo htmlspecialchars($upload['filename']); ?>" target="_blank">
                            <img src="uploads/<?php echo htmlspecialchars($upload['filename']); ?>" 
                                 alt="Uploaded Image" 
                                 class="h-48 w-full object-cover rounded mb-4">
                        </a>
                    <?php else: ?>
                        <div class="text-gray-400 italic mb-4">[File not previewable]</div>
                    <?php endif; ?>

                    <p class="text-gray-700 text-sm text-center mb-4">
                        Uploaded by <span class="font-semibold"><?php echo htmlspecialchars($upload['username']); ?></span><br>
                        <span class="text-gray-400 text-xs"><?php echo date('F j, Y', strtotime($upload['uploaded_at'])); ?></span>
                    </p>

                    <a href="delete_upload.php?id=<?php echo $upload['id']; ?>" 
                       class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-3 rounded"
                       onclick="return confirm('Are you sure you want to delete this upload?');">
                       Delete
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-600">No uploads found.</p>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>

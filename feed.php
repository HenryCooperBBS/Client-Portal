<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<?php include 'templates/header.php'; ?>

<div class="max-w-5xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-8 text-center">Recent Uploads</h2>

    <?php
    $stmt = $pdo->query("SELECT uploads.filename, uploads.uploaded_at, users.username 
                         FROM uploads 
                         JOIN users ON uploads.user_id = users.id 
                         ORDER BY uploads.uploaded_at DESC 
                         LIMIT 30"); // Show the 30 most recent uploads

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
                        <div class="text-gray-400 italic">[File not previewable]</div>
                    <?php endif; ?>
                    
                    <p class="text-center text-lg font-semibold mb-2">
                        <?php echo htmlspecialchars($upload['name']); ?>
                    </p>

                    <?php if (!empty($upload['comment'])): ?>
                        <p class="text-gray-600 text-center mb-4">
                            "<?php echo htmlspecialchars($upload['comment']); ?>"
                        </p>
                    <?php endif; ?>

                    <p class="text-gray-500 text-xs text-center">
                        Uploaded by <span class="font-semibold"><?php echo htmlspecialchars($upload['username']); ?></span><br>
                        <?php echo date('F j, Y', strtotime($upload['uploaded_at'])); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-600">No uploads yet. Be the first!</p>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>

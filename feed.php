<?php
session_start();
require_once 'includes/db.php';

// Figure out user's group
$userGroup = 'public'; // Default
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT user_group FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    if ($user) {
        $userGroup = $user['user_group'];
    }
}

// Fetch only projects user can see
$stmt = $pdo->prepare("SELECT uploads.id, uploads.filename, uploads.name, uploads.comment, uploads.link, uploads.github_link, uploads.visibility, uploads.uploaded_at, users.username 
                       FROM uploads 
                       JOIN users ON uploads.user_id = users.id 
                       WHERE uploads.visibility = 'public' OR uploads.visibility = ?
                       ORDER BY uploads.uploaded_at DESC 
                       LIMIT 30");
$stmt->execute([$userGroup]);
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

                    <div class="flex items-center justify-center space-x-2 mt-4">
                        <?php if (!empty($upload['link'])): ?>
                            <a href="<?php echo htmlspecialchars($upload['link']); ?>" target="_blank" 
                            class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-2 px-4 rounded">
                                View Project
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($upload['github_link'])): ?>
                            <a href="<?php echo htmlspecialchars($upload['github_link']); ?>" target="_blank"
                            class="inline-flex items-center justify-center bg-gray-800 hover:bg-gray-900 text-white p-2 rounded">
                                <!-- GitHub SVG Logo -->
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 .5C5.6.5.5 5.6.5 12c0 5.1 3.3 9.4 7.9 10.9.6.1.8-.2.8-.5v-1.7c-3.2.7-3.9-1.5-3.9-1.5-.5-1.2-1.2-1.5-1.2-1.5-1-.7.1-.7.1-.7 1.1.1 1.7 1.2 1.7 1.2 1 .1 1.5 2.2 1.5 2.2.9 1.5 2.5 1 3.1.8.1-.7.4-1 .7-1.3-2.6-.3-5.3-1.3-5.3-6 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3 0 0 1-.3 3.3 1.2a11.5 11.5 0 016 0c2.3-1.5 3.3-1.2 3.3-1.2.6 1.5.2 2.7.1 3 .8.8 1.2 1.9 1.2 3.2 0 4.7-2.7 5.7-5.3 6 .4.3.7.9.7 1.7v2.5c0 .3.2.7.8.5A10.5 10.5 0 0023.5 12c0-6.4-5.1-11.5-11.5-11.5z"/>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>


                    <?php if (!empty($upload['link'])): ?>
                        <a href="<?php echo htmlspecialchars($upload['link']); ?>" target="_blank" 
                        class="mt-2 inline-block bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-2 px-4 rounded">
                            View Project
                        </a>
                    <?php endif; ?>

                    <p class="text-gray-500 text-xs text-center">
                        Uploaded by <span class="font-semibold"><?php echo htmlspecialchars($upload['username']); ?></span><br>
                        <?php echo date('F j, Y', strtotime($upload['uploaded_at'])); ?>
                    </p>

                    <?php
                    // Count total likes for this upload
                    $likeStmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE upload_id = ?");
                    $likeStmt->execute([$upload['id']]);
                    $likeCount = $likeStmt->fetchColumn();
                    ?>

                    <div class="flex items-center space-x-2 mt-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <button onclick="likeUpload(<?php echo $upload['id']; ?>)" 
                                class="text-red-500 hover:text-red-700 text-xl">
                            ❤️
                        </button>
                    <?php else: ?>
                        <div class="text-gray-400 text-xl cursor-not-allowed" title="Login to like projects">
                            ❤️
                        </div>
                    <?php endif; ?>
                        <span id="like-count-<?php echo $upload['id']; ?>" class="text-gray-700 text-sm">
                            <?php echo $likeCount; ?> Like<?php echo ($likeCount == 1) ? '' : 's'; ?>
                        </span>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-600">No uploads yet. Be the first!</p>
    <?php endif; ?>
</div>

<script>
function likeUpload(uploadId) {
    fetch('like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'upload_id=' + uploadId
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById('like-count-' + uploadId).innerText = data + ' Like' + (data == 1 ? '' : 's');
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>

<?php include 'templates/footer.php'; ?>

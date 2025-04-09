<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Fetch the current user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$currentUser = $stmt->fetch();

// Only allow admins
if (!$currentUser || $currentUser['is_admin'] != 1) {
    header("Location: feed.php"); // Redirect normal users to Projects
    exit;
}
?>

<?php include 'templates/header.php'; ?>

<div class="max-w-3xl mx-auto p-4">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-6">
        <h2 class="text-2xl font-bold mb-6 text-center">Upload a File</h2>

        <?php if (isset($_SESSION['flash_upload'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php 
                echo $_SESSION['flash_upload']; 
                unset($_SESSION['flash_upload']); 
                ?>
            </div>
        <?php endif; ?>

        <form action="upload.php" method="POST" enctype="multipart/form-data">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Upload Name</label>
                <input type="text" name="name" id="name"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Give your upload a name" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="comment">Comment (optional)</label>
                <textarea name="comment" id="comment"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Add a comment..." rows="3"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="link">Project Link (optional)</label>
                <input type="url" name="link" id="link"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="https://example.com/">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="github_link">GitHub Link (optional)</label>
                <input type="url" name="github_link" id="github_link"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="https://github.com/your-repo">
            </div>
            <div class="mb-4">
                <input type="file" name="file" required
                    class="block w-full text-sm text-gray-700 border border-gray-300 rounded">
            </div>

            <button type="submit" name="upload"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full">
                Upload
            </button>
        </form>

    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-6">
        <h3 class="text-xl font-bold mb-4">Your Files</h3>

        <?php
        $stmt = $pdo->prepare("SELECT * FROM uploads WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $uploads = $stmt->fetchAll();
        ?>

        <?php if ($uploads): ?>
            <ul>
                <?php foreach ($uploads as $upload): ?>
                    <li class="mb-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <a class="text-blue-500 hover:text-blue-700" href="uploads/<?php echo htmlspecialchars($upload['filename']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($upload['filename']); ?>
                                </a> 
                                <span class="text-gray-400 text-sm">(Uploaded on <?php echo date('F j, Y', strtotime($upload['uploaded_at'])); ?>)</span>

                                <?php
                                $ext = strtolower(pathinfo($upload['filename'], PATHINFO_EXTENSION));
                                if (in_array($ext, ['jpg', 'jpeg', 'png'])):
                                ?>
                                    <div class="mt-2">
                                        <a href="uploads/<?php echo htmlspecialchars($upload['filename']); ?>" target="_blank">
                                            <img src="uploads/<?php echo htmlspecialchars($upload['filename']); ?>" alt="Preview" class="h-24 rounded shadow">
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <a href="delete.php?id=<?php echo $upload['id']; ?>" 
                                   class="ml-4 bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-600">You haven't uploaded any files yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the project ID
if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$uploadId = (int)$_GET['id'];

// Fetch project details
$stmt = $pdo->prepare("SELECT * FROM uploads WHERE id = ?");
$stmt->execute([$uploadId]);
$upload = $stmt->fetch();

// Security check: Only the owner (you) or admin can edit
if (!$upload || $upload['user_id'] != $_SESSION['user_id']) {
    header("Location: dashboard.php");
    exit;
}

// Handle form submit
if (isset($_POST['update'])) {
    $name = trim($_POST['name']);
    $comment = trim($_POST['comment']);
    $link = trim($_POST['link']);
    $github_link = trim($_POST['github_link']);
    
    // Check if new image was uploaded
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === 0) {
        $file = $_FILES['new_image'];
        $allowedTypes = ['image/jpeg', 'image/png'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        
        if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxFileSize) {
            $newFilename = time() . '_' . basename($file['name']);
            $destination = 'uploads/' . $newFilename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Delete old file
                $oldFile = 'uploads/' . $upload['filename'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                
                // Update database with new filename
                $stmt = $pdo->prepare("UPDATE uploads SET name = ?, comment = ?, link = ?, github_link = ?, filename = ? WHERE id = ?");
                $stmt->execute([$name, $comment, $link, $github_link, $newFilename, $uploadId]);
            }
        }
    } else {
        // No new image uploaded, just update text fields
        $stmt = $pdo->prepare("UPDATE uploads SET name = ?, comment = ?, link = ?, github_link = ? WHERE id = ?");
        $stmt->execute([$name, $comment, $link, $github_link, $uploadId]);
    }

    header("Location: dashboard.php");
    exit;
}
?>

<?php include 'templates/header.php'; ?>

<div class="max-w-3xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-8 text-center">Edit Project</h2>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Project Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($upload['name']); ?>"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Comment</label>
            <textarea name="comment" rows="3"
                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"><?php echo htmlspecialchars($upload['comment']); ?></textarea>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Project Link</label>
            <input type="url" name="link" value="<?php echo htmlspecialchars($upload['link']); ?>"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">GitHub Link</label>
            <input type="url" name="github_link" value="<?php echo htmlspecialchars($upload['github_link']); ?>"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Current Image</label>
            <img src="uploads/<?php echo htmlspecialchars($upload['filename']); ?>" alt="Current Project Image" class="h-40 mb-4 rounded shadow">
            
            <label class="block text-gray-700 text-sm font-bold mb-2">Change Image (optional)</label>
            <input type="file" name="new_image"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="flex justify-between">
            <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Cancel
            </a>
            <button type="submit" name="update" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Update Project
            </button>
        </div>
    </form>
</div>

<?php include 'templates/footer.php'; ?>

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
                <label class="block text-gray-700 text-sm font-bold mb-2" for="visibility">Visibility</label>
                <select name="visibility" id="visibility"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="public">Public</option>
                    <option value="client">Client Only</option>
                    <option value="internal">Internal Only</option>
                </select>
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

    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-3xl font-bold mb-8 text-center">Your Projects</h2>

        <?php
        $stmt = $pdo->prepare("SELECT * FROM uploads WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $uploads = $stmt->fetchAll();
        ?>

        <?php if ($uploads): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($uploads as $upload): ?>
                    <div class="bg-white shadow-md rounded-lg overflow-hidden p-4 flex flex-col">
                        <!-- Image -->
                        <img src="uploads/<?php echo htmlspecialchars($upload['filename']); ?>" 
                            alt="Project Image" 
                            class="h-48 w-full object-cover mb-4 rounded">

                        <!-- Project Info -->
                        <h3 class="text-lg font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($upload['name']); ?></h3>

                        <?php if (!empty($upload['comment'])): ?>
                            <p class="text-gray-600 mb-4 text-sm"><?php echo htmlspecialchars($upload['comment']); ?></p>
                        <?php endif; ?>

                        <!-- Links -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <?php if (!empty($upload['link'])): ?>
                                <a href="<?php echo htmlspecialchars($upload['link']); ?>" target="_blank"
                                class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-2 rounded">
                                    View Project
                                </a>
                            <?php endif; ?>

                            <?php if (!empty($upload['github_link'])): ?>
                                <a href="<?php echo htmlspecialchars($upload['github_link']); ?>" target="_blank"
                                class="inline-flex items-center justify-center bg-gray-800 hover:bg-gray-900 text-white p-2 rounded">
                                    <!-- GitHub SVG Icon -->
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 .5C5.6.5.5 5.6.5 12c0 5.1 3.3 9.4 7.9 10.9.6.1.8-.2.8-.5v-1.7c-3.2.7-3.9-1.5-3.9-1.5-.5-1.2-1.2-1.5-1.2-1.5-1-.7.1-.7.1-.7 1.1.1 1.7 1.2 1.7 1.2 1 .1 1.5 2.2 1.5 2.2.9 1.5 2.5 1 3.1.8.1-.7.4-1 .7-1.3-2.6-.3-5.3-1.3-5.3-6 0-1.3.5-2.4 1.2-3.2-.1-.3-.5-1.5.1-3 0 0 1-.3 3.3 1.2a11.5 11.5 0 016 0c2.3-1.5 3.3-1.2 3.3-1.2.6 1.5.2 2.7.1 3 .8.8 1.2 1.9 1.2 3.2 0 4.7-2.7 5.7-5.3 6 .4.3.7.9.7 1.7v2.5c0 .3.2.7.8.5A10.5 10.5 0 0023.5 12c0-6.4-5.1-11.5-11.5-11.5z"/>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-between mt-auto">
                            <a href="edit_project.php?id=<?php echo $upload['id']; ?>"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-bold py-2 px-4 rounded">
                                Edit
                            </a>
                            <a href="delete.php?id=<?php echo $upload['id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this project?');"
                            class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-2 px-4 rounded">
                                Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-600">You haven't uploaded any projects yet.</p>
        <?php endif; ?>
    </div>

<?php include 'templates/footer.php'; ?>

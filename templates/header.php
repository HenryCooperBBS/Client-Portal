<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Top Navbar -->
<nav class="bg-white shadow mb-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="dashboard.php" class="text-xl font-bold text-gray-800">Client Portal</a>
            </div>
            <div class="flex items-center space-x-4">
                <a href="dashboard.php" class="text-gray-700 hover:text-gray-900">Upload</a>
                <a href="feed.php" class="text-gray-700 hover:text-gray-900">Feed</a>

                <?php
                if (isset($_SESSION['user_id'])) {
                    require_once __DIR__ . '/../includes/db.php';

                    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();

                    if ($user && $user['is_admin']) {
                        echo '<a href="admin.php" class="text-gray-700 hover:text-gray-900">Admin</a>';
                    }
                }
                ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>


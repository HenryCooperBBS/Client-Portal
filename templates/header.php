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
                <a href="dashboard.php" class="text-xl font-bold text-gray-800">Henrys Portfolio</a>
            </div>
            <div class="flex items-center space-x-4">
                <a href="dashboard.php" class="text-gray-700 hover:text-gray-900">Upload</a>
                <a href="feed.php" class="text-gray-700 hover:text-gray-900">Projects</a>
                <a href="about.php" class="text-gray-700 hover:text-gray-900">About</a>

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

                <?php
                $notificationCount = 0;
                $latestNotifications = [];
                if (isset($_SESSION['user_id'])) {
                    require_once __DIR__ . '/../includes/db.php';

                    // Count unread
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
                    $stmt->execute([$_SESSION['user_id']]);
                    $notificationCount = $stmt->fetchColumn();

                    // Fetch latest 5 notifications
                    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
                    $stmt->execute([$_SESSION['user_id']]);
                    $latestNotifications = $stmt->fetchAll();
                }
                ?>

                <!-- Notifications Bell + Dropdown -->
                <div class="relative group">
                    <button class="relative text-gray-700 hover:text-gray-900 focus:outline-none mr-6">
                        🔔
                        <?php if ($notificationCount > 0): ?>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full px-1">
                                <?php echo $notificationCount; ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <!-- Dropdown -->
                    <div class="absolute right-0 mt-2 w-72 bg-white shadow-md rounded hidden group-hover:block z-50">
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Notifications</h3>

                            <?php if ($latestNotifications): ?>
                                <ul class="space-y-2 mb-4">
                                    <?php foreach ($latestNotifications as $notification): ?>
                                        <li class="text-gray-600 text-sm <?php echo !$notification['is_read'] ? 'font-bold' : ''; ?>">
                                            <?php echo htmlspecialchars($notification['message']); ?>
                                            <br>
                                            <span class="text-gray-400 text-xs">
                                                <?php echo date('M j, g:i a', strtotime($notification['created_at'])); ?>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>

                                <!-- Clear All Notifications Button -->
                                <form action="clear_notifications.php" method="POST">
                                    <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-2 px-4 rounded">
                                        Clear All
                                    </button>
                                </form>

                            <?php else: ?>
                                <p class="text-gray-400 text-xs">No notifications.</p>
                            <?php endif; ?>
                        </div>
                    </div>


                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
function fetchNotifications() {
    fetch('fetch_notifications.php')
        .then(response => response.json())
        .then(data => {
            // Update bell badge
            const badge = document.querySelector('.notification-badge');
            if (badge) badge.remove();

            if (data.count > 0) {
                const bell = document.querySelector('.group button');
                const badgeHtml = '<span class="notification-badge absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full px-1">'
                                  + data.count + '</span>';
                bell.insertAdjacentHTML('beforeend', badgeHtml);
            }

            // Update dropdown list
            const dropdown = document.querySelector('.group .p-4 ul');
            if (dropdown) {
                dropdown.innerHTML = '';
                data.notifications.forEach(notification => {
                    dropdown.innerHTML += `
                        <li class="text-gray-600 text-sm ${notification.is_read == 0 ? 'font-bold' : ''}">
                            ${notification.message}<br>
                            <span class="text-gray-400 text-xs">${notification.created_at}</span>
                        </li>
                    `;
                });
            }
        });
}

// Auto-refresh every 5 seconds
setInterval(fetchNotifications, 5000);
</script>


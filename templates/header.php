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
<!-- Top Navbar -->
<nav class="bg-gradient-to-r from-blue-500 to-purple-600 shadow-md">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Left: Logo -->
            <div class="flex items-center">
                <a href="feed.php" class="text-2xl font-extrabold text-white drop-shadow">
                    Henry's Portfolio
                </a>
            </div>

            <!-- Right: Links -->
            <div class="flex items-center space-x-6">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    require_once __DIR__ . '/../includes/db.php';
                    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();
                    ?>

                    <?php if ($user && $user['is_admin'] == 1): ?>
                        <a href="dashboard.php" class="text-white hover:text-gray-200 font-semibold transition">
                            Upload
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

                <a href="feed.php" class="text-white hover:text-gray-200 font-semibold transition">
                    Projects
                </a>

                <a href="about.php" class="text-white hover:text-gray-200 font-semibold transition">
                    About
                </a>

                <?php
                if (isset($_SESSION['user_id'])) {
                    require_once __DIR__ . '/../includes/db.php';
                    $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();

                    if ($user && $user['is_admin']) {
                        echo '<a href="admin.php" class="text-white hover:text-gray-200 font-semibold transition">Admin</a>';
                    }
                }
                ?>

                <!-- Notifications Bell + Dropdown -->
                <?php
                $notificationCount = 0;
                $latestNotifications = [];
                if (isset($_SESSION['user_id'])) {
                    require_once __DIR__ . '/../includes/db.php';
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
                    $stmt->execute([$_SESSION['user_id']]);
                    $notificationCount = $stmt->fetchColumn();

                    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
                    $stmt->execute([$_SESSION['user_id']]);
                    $latestNotifications = $stmt->fetchAll();
                }
                ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="relative group">
                        <button class="relative text-white hover:text-gray-200 text-2xl focus:outline-none">
                            🔔
                            <?php if ($notificationCount > 0): ?>
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full px-1">
                                    <?php echo $notificationCount; ?>
                                </span>
                            <?php endif; ?>
                        </button>

                        <!-- Dropdown -->
                        <div class="absolute right-0 mt-2 w-72 bg-white shadow-lg rounded hidden group-hover:block z-50">
                            <div class="p-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Notifications</h3>
                                <?php if ($latestNotifications): ?>
                                    <ul class="space-y-2">
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
                                <?php else: ?>
                                    <p class="text-gray-400 text-xs">No notifications.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition">
                        Logout
                    </a>
                <?php else: ?>
                    <a href="index.php" class="bg-green-400 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full transition">
                        Login
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


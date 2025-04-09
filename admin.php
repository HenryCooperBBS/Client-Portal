<?php
session_start();
require_once 'includes/db.php';

// Admin check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['is_admin'] != 1) {
    header('Location: feed.php');
    exit;
}

// Handle New Group Creation
if (isset($_POST['create_group'])) {
    $groupName = trim($_POST['group_name']);
    if (!empty($groupName)) {
        $stmt = $pdo->prepare("INSERT INTO user_groups (name) VALUES (?)");
        $stmt->execute([$groupName]);
    }
}

// Handle Updating User Group
if (isset($_POST['update_group'])) {
    $userId = (int)$_POST['user_id'];
    $groupId = (int)$_POST['group_id'];
    $stmt = $pdo->prepare("UPDATE users SET group_id = ? WHERE id = ?");
    $stmt->execute([$groupId, $userId]);
}

// Fetch All Groups
$stmt = $pdo->query("SELECT * FROM user_groups");
$groups = $stmt->fetchAll();

// Fetch All Users
$stmt = $pdo->query("SELECT users.*, user_groups.name AS group_name FROM users LEFT JOIN user_groups ON users.group_id = user_groups.id");
$users = $stmt->fetchAll();
?>

<?php include 'templates/header.php'; ?>

<div class="flex min-h-screen">
    <!-- Sidebar -->
    <?php include 'templates/admin-sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-6 max-w-6xl mx-auto">
        <h2 class="text-3xl font-bold mb-8 text-center">Admin Panel</h2>

        <!-- Create New Group -->
        <div class="mb-12">
            <h3 class="text-2xl font-semibold mb-4">Create New Group</h3>

            <form method="POST" class="flex items-center space-x-4">
                <input type="text" name="group_name" required
                       placeholder="New Group Name"
                       class="flex-1 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <button type="submit" name="create_group"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Create Group
                </button>
            </form>
        </div>

        <!-- List Existing Groups -->
        <div class="mb-12">
            <h3 class="text-2xl font-semibold mb-4">Existing Groups</h3>

            <ul class="grid grid-cols-2 gap-4">
                <?php foreach ($groups as $group): ?>
                    <li class="bg-white shadow rounded p-4 text-gray-700">
                        <?php echo htmlspecialchars($group['name']); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Manage Users -->
        <div>
            <h3 class="text-2xl font-semibold mb-4">Manage Users</h3>

            <table class="min-w-full bg-white rounded shadow-md mb-12">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-left">
                        <th class="py-2 px-4">Username</th>
                        <th class="py-2 px-4">Current Group</th>
                        <th class="py-2 px-4">Assign Group</th>
                        <th class="py-2 px-4">Manage User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-t">
                            <td class="py-2 px-4"><?php echo htmlspecialchars($user['username']); ?></td>
                            <td class="py-2 px-4"><?php echo htmlspecialchars($user['group_name'] ?? 'None'); ?></td>
                            <td class="py-2 px-4">
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <select name="group_id"
                                            class="shadow border rounded py-1 px-2 text-gray-700">
                                        <option value="">None</option>
                                        <?php foreach ($groups as $group): ?>
                                            <option value="<?php echo $group['id']; ?>"
                                                <?php echo ($user['group_id'] == $group['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($group['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_group"
                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded">
                                        Update
                                    </button>
                                </form>
                            </td>
                            <td class="py-2 px-4 flex space-x-2">
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>"
                                class="bg-yellow-400 hover:bg-yellow-500 text-white text-xs font-bold py-1 px-3 rounded">
                                    Edit
                                </a>
                                <a href="delete_user.php?id=<?php echo $user['id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this user?');"
                                class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-3 rounded">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>


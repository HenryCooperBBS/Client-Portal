<?php
session_start();
require_once 'includes/db.php';

// Admin Check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if admin
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['is_admin'] != 1) {
    header('Location: feed.php');
    exit;
}

// Create New Group
if (isset($_POST['create_group'])) {
    $groupName = trim($_POST['group_name']);
    if (!empty($groupName)) {
        $stmt = $pdo->prepare("INSERT INTO user_groups (name) VALUES (?)");
        $stmt->execute([$groupName]);
    }
}

// Fetch all groups
$stmt = $pdo->query("SELECT * FROM user_groups");
$groups = $stmt->fetchAll();
?>

<?php include 'templates/header.php'; ?>

<div class="max-w-3xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-8 text-center">Manage Groups</h2>

    <form method="POST" class="mb-6">
        <div class="flex items-center space-x-4">
            <input type="text" name="group_name" required
                   placeholder="New Group Name"
                   class="flex-1 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <button type="submit" name="create_group"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Create Group
            </button>
        </div>
    </form>

    <div>
        <h3 class="text-xl font-bold mb-4">Existing Groups</h3>
        <ul class="space-y-2">
            <?php foreach ($groups as $group): ?>
                <li class="bg-white shadow-md rounded px-4 py-2 text-gray-700">
                    <?php echo htmlspecialchars($group['name']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

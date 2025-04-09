<?php
session_start();
require_once 'includes/db.php';

// Check admin
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

// Fetch user info
if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$userId = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$userToEdit = $stmt->fetch();

if (!$userToEdit) {
    header('Location: admin.php');
    exit;
}

// Handle form submission
if (isset($_POST['update_user'])) {
    $username = trim($_POST['username']);
    $groupId = (int)$_POST['group_id'];
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE users SET username = ?, group_id = ?, is_admin = ? WHERE id = ?");
    $stmt->execute([$username, $groupId, $isAdmin, $userId]);
    
    if (!empty($_POST['password'])) {
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashedPassword, $userId]);
    }

    header('Location: admin.php');
    exit;
}

// Fetch all groups
$groupStmt = $pdo->query("SELECT * FROM user_groups ORDER BY name ASC");
$groups = $groupStmt->fetchAll();
?>

<?php include 'templates/header.php'; ?>

<div class="max-w-2xl mx-auto p-6">
    <h2 class="text-3xl font-bold mb-8 text-center">Edit User</h2>

    <form method="POST" class="space-y-6">
        <div>
            <label class="block text-gray-700 font-bold mb-2" for="username">Username</label>
            <input type="text" name="username" id="username" required
                   value="<?php echo htmlspecialchars($userToEdit['username']); ?>"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_admin" value="1"
                    <?php echo ($userToEdit['is_admin'] == 1) ? 'checked' : ''; ?>
                    class="form-checkbox text-indigo-600">
                <span class="ml-2 text-gray-700">Administrator</span>
            </label>
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2" for="password">New Password (optional)</label>
            <input type="password" name="password" id="password"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div>
            <label class="block text-gray-700 font-bold mb-2" for="group_id">Group</label>
            <select name="group_id" id="group_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="">None</option>
                <?php foreach ($groups as $group): ?>
                    <option value="<?php echo $group['id']; ?>"
                        <?php echo ($userToEdit['group_id'] == $group['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars(ucfirst($group['name'])); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" name="update_user"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Save Changes
        </button>
    </form>
</div>

<?php include 'templates/footer.php'; ?>

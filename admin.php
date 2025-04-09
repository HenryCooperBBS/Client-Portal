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

// Check if current user is admin
if (!$currentUser || $currentUser['is_admin'] != 1) {
    header("Location: dashboard.php");
    exit;
}
?>

<?php include 'templates/header.php'; ?>

<div class="flex">

<?php include 'includes/admin-sidebar.php'; ?>

<div class="w-full max-w-3xl mx-auto mt-10">
    <h2 class="text-3xl font-bold mb-6 text-center">Admin Portal - Manage Users</h2>

    <?php if (isset($_SESSION['flash_admin'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">
            <?php 
            echo $_SESSION['flash_admin']; 
            unset($_SESSION['flash_admin']); 
            ?>
        </div>
    <?php endif; ?>

    <?php
    // Fetch all users
    $stmt = $pdo->query("SELECT id, username, email, created_at, is_admin FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
    ?>

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Username
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Member Since
                    </th>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Admin
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                        <?php echo htmlspecialchars($user['email']); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                        <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-center">
                        <?php echo $user['is_admin'] ? '✅' : '❌'; ?>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 text-sm text-right">
                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <a href="toggle_admin.php?id=<?php echo $user['id']; ?>"
                            class="bg-yellow-400 hover:bg-yellow-600 text-white text-xs font-bold py-1 px-3 rounded mr-2">
                            <?php echo $user['is_admin'] ? 'Demote' : 'Promote'; ?>
                            </a>

                            <a href="delete_user.php?id=<?php echo $user['id']; ?>"
                            class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-3 rounded"
                            onclick="return confirm('Are you sure you want to delete this user?');">
                            Delete
                            </a>
                        <?php else: ?>
                            <span class="text-gray-400 text-xs">(You)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>


        </table>
    </div>

    <div class="text-center mt-6">
        <a href="dashboard.php" class="text-blue-500 hover:text-blue-700">← Back to Dashboard</a>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

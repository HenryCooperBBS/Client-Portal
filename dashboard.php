<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<?php include 'templates/header.php'; ?>

<div class="w-full max-w-md mx-auto mt-10">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 text-center">
        <h2 class="text-2xl font-bold mb-2">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h2>
        <p class="text-gray-600 mb-4">Email: <?php echo htmlspecialchars($user['email']); ?></p>
        <p class="text-gray-500 text-sm">Member since <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>

        <a href="logout.php" class="mt-6 inline-block bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
            Logout
        </a>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

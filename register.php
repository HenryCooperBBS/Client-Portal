<?php
session_start();
require_once 'includes/db.php';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);

    if (empty($username) || empty($password)) {
        $_SESSION['flash_error'] = "Username and password are required.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$username, $hashedPassword, $email]);
            $_SESSION['flash_success'] = "Account created successfully! Please log in.";
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $_SESSION['flash_error'] = "Username already taken.";
        }
    }
}
?>

<?php include 'templates/header.php'; ?>

<div class="w-full max-w-xs mx-auto mt-10">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-6 text-center">Register</h2>

        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php 
                echo $_SESSION['flash_error']; 
                unset($_SESSION['flash_error']); 
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="username">Username</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       type="text" name="username" id="username" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       type="email" name="email" id="email">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline"
                       type="password" name="password" id="password" required>
            </div>

            <div class="flex items-center justify-between">
                <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit" name="register">
                    Register
                </button>
            </div>
        </form>

        <p class="text-center text-gray-600 text-sm mt-4">
            Already have an account? <a class="text-blue-500 hover:text-blue-700" href="index.php">Login</a>
        </p>
    </div>
</div>

<?php include 'templates/footer.php'; ?>

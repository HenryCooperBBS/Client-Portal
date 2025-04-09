<?php
session_start();
require_once 'includes/db.php';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $email = trim($_POST['email']);

    if (empty($username) || empty($password)) {
        $error = "Username and password are required.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$username, $hashedPassword, $email]);
            header("Location: index.php?registered=1");
            exit;
        } catch (PDOException $e) {
            $error = "Username already taken.";
        }
    }
}
?>

<?php include 'templates/header.php'; ?>

<h2>Register</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST" action="">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="email" name="email" placeholder="Email"><br>
    <button type="submit" name="register">Register</button>
</form>

<p><a href="index.php">Back to Login</a></p>

<?php include 'templates/footer.php'; ?>

<?php
session_start();
ob_start(); // Start output buffering

require 'includes/db.php'; // Use PDO instead of MySQLi

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST["login"]); // Username or email
    $password = $_POST["password"];

    try {
        // Retrieve user data from the database (PDO prevents SQL Injection)
        $stmt = $pdo->prepare("SELECT id, username, role, password FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login, $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            // Successful login: Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php"); // Redirect to homepage
            exit();
        } else {
            // Generic error message (Avoid exposing valid/invalid username detection)
            $error = "Invalid login credentials.";
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage()); // For debugging (remove in production)
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

    <h1>Login</h1>

    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    
            <label for="login">Username:</label>
            <input type="text" id="login" name="login" required>
            <br><br>

            <label for="login">Email:</label>
            <input type="text" id="login" name="login" required>
             <br><br>
    
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
             <br><br>
        <button type="submit">Login</button>
    </form>

</body>
</html>

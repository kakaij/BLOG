<?php
session_start();
ob_start(); // Start output buffering

require 'includes/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Input validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = "Username or email is already in use.";
            } else {
                // Hash password and insert user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                if ($stmt->execute([$username, $email, $hashed_password])) {
                    $_SESSION['success'] = "Registration successful. Please log in.";
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage()); // For debugging (remove in production)
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Registration</h1>

    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        <br><br>
        
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        <br><br>
        
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        <br><br>
        <button type="submit">Register</button>
    </form>
</body>
</html>

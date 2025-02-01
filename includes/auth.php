<?php
session_start();

function register($username, $email, $password, $pdo) {
    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword]);
    } catch (PDOException $e) {
        die("Registration failed: " . $e->getMessage()); // Debugging (remove in production)
    }
}

function login($email, $password, $pdo) {
    session_start(); // Ensure session is started

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            return true;
        }
    } catch (PDOException $e) {
        die("Login failed: " . $e->getMessage()); // Debugging (remove in production)
    }

    return false;
}
?>

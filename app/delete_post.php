<?php
session_start();
require '../includes/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ensure 'id' is provided in the URL
if (!isset($_GET['id'])) {
    die("Post ID is missing.");
}

$post_id = intval($_GET['id']);

// Ensure the post belongs to the logged-in user
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch();

if (!$post) {
    die("Post not found or you do not have permission to delete it.");
}

// Delete the post from the database
$stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
$stmt->execute([$post_id]);

// Redirect to the homepage or another page after deletion
header("Location: index.php");
exit;
?>

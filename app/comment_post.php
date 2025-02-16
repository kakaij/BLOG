<?php
session_start();
require '../includes/db.php';

// Ensure post ID is provided and is valid
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Post ID is missing or invalid.");
}

$post_id = intval($_GET['id']);

// Fetch the post along with the username
$stmt = $pdo->prepare("SELECT posts.*, categories.name AS category_name, users.username 
                       FROM posts 
                       JOIN categories ON posts.category_id = categories.id 
                       JOIN users ON posts.user_id = users.id 
                       WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();

// If post doesn't exist
if (!$post) {
    die("Post not found.");
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $comment = trim($_POST['comment']);
    $user_id = $_SESSION['user_id'];

    if (!empty($comment)) {
        // Insert comment into the database
        $stmt = $pdo->prepare("INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)");
        $stmt->execute([$post_id, $user_id, $comment]);
        header("Location: view.php?id=$post_id"); // Reload the page to show the new comment
        exit();
    } else {
        $error = "Please enter a comment.";
    }
}

// Fetch comments for this post
$comments_stmt = $pdo->prepare("SELECT comments.*, users.username 
                                FROM comments
                                JOIN users ON comments.user_id = users.id
                                WHERE comments.post_id = ?
                                ORDER BY comments.created_at DESC");
$comments_stmt->execute([$post_id]);
$comments = $comments_stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Post</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4"><?= htmlspecialchars($post['title']) ?></h1>
        <p><strong>Category:</strong> <?= htmlspecialchars($post['category_name']) ?></p>
        <p><strong>Posted by:</strong> <?= htmlspecialchars($post['username']) ?></p>
        <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>

        <!-- Display Comments -->
        <h3>Comments</h3>
        <?php foreach ($comments as $comment): ?>
            <div class="card my-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($comment['username']) ?></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($comment['comment'])) ?></p>
                    <p class="text-muted"><?= $comment['created_at'] ?></p>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Add a Comment Form -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <h4>Leave a Comment</h4>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form action="view.php?id=<?= $post_id ?>" method="POST">
                <div class="form-group">
                    <textarea name="comment" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Comment</button>
            </form>
        <?php else: ?>
            <p>Please <a href="login.php">login</a> to comment.</p>
        <?php endif; ?>
    </div>
</body>
</html>

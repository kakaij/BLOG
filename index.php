<?php
session_start();
require 'includes/db.php'; // Ensuring that this file correctly sets up $pdo

try {
    // Fetch posts from the database
    $posts = $pdo->query("
        SELECT posts.*, users.username, categories.name AS category_name 
        FROM posts
        JOIN users ON posts.user_id = users.id
        JOIN categories ON posts.category_id = categories.id
        ORDER BY posts.created_at DESC
    ")->fetchAll();
} catch (PDOException $e) {
    die("Error fetching posts: " . $e->getMessage()); // Debugging (remove in production)
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">git -m 
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            margin: 20px;
            font-family: Arial, sans-serif;
        }
        .post {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .post h2 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">Blog Posts</h1>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Links for logged-in users -->
            <a href="/BlogApp/app/create_post.php" class="btn btn-primary mb-4">Create New Post</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/BlogApp/app/admin.php" class="btn btn-secondary mb-4">Admin Panel</a>
            <?php endif; ?>
            <a href="/BlogApp/app/logout.php" class="btn btn-danger mb-4">Logout</a>
        <?php else: ?>
            <!-- Links for non-logged-in users -->
            <a href="/BlogApp/app/login.php" class="btn btn-primary mb-4">Login</a>
            <a href="/BlogApp/app/registration.php" class="btn btn-secondary mb-4">Register</a>
        <?php endif; ?>

        <div class="posts">
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <h2><?= htmlspecialchars($post['title']) ?></h2>
                    <p>by <?= htmlspecialchars($post['username']) ?> in <?= htmlspecialchars($post['category_name']) ?></p>
                    <p><?= nl2br(htmlspecialchars(mb_substr($post['content'], 0, 200))) ?>...</p>
                    <a href="/BlogApp/app/view_post.php?id=<?= $post['id'] ?>" class="btn btn-info">Read More</a>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                        <a href="/BlogApp/app/edit_post.php?id=<?= $post['id'] ?>" class="btn btn-warning">Edit</a>
                        <a href="/BlogApp/app/delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>

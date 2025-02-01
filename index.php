<?php
session_start();
require 'includes/db.php'; // Ensure database connection is set up

try {
    // Fetch posts from the database with category and user info
    $stmt = $pdo->query("
        SELECT posts.*, users.username, categories.name AS category_name 
        FROM posts
        JOIN users ON posts.user_id = users.id
        JOIN categories ON posts.category_id = categories.id
        ORDER BY posts.created_at DESC
    ");
    $posts = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching posts: " . $e->getMessage()); // Debugging (remove in production)
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Home</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            margin: 20px;
            font-family: Arial, sans-serif;
            background-color:rgb(109, 134, 159);
        }
        .post {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: white;
        }
        .post h2 {
            margin-top: 0;
        }
        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="index.php">J's Blog</a>
        <div class="ml-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="app/create_post.php" class="btn btn-success">Create New Post</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin/admin.php" class="btn btn-warning">Admin Panel</a>
                <?php endif; ?>
                <a href="app/logout.php" class="btn btn-danger">Logout</a>
            <?php else: ?>
                <a href="app/login.php" class="btn btn-primary">Login</a>
                <a href="app/registration.php" class="btn btn-secondary">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Blog Posts Container -->
    <div class="container">
        <h1 class="mb-4">Latest Blog Posts</h1>

        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <h2><?= htmlspecialchars($post['title']) ?></h2>
                    <p><strong>By:</strong> <?= htmlspecialchars($post['username']) ?> | <strong>Category:</strong> <?= htmlspecialchars($post['category_name']) ?></p>
                    <p><?= nl2br(htmlspecialchars(mb_substr($post['content'], 0, 200))) ?>...</p>
                    <a href="app/view_post.php?id=<?= $post['id'] ?>" class="btn btn-info">Read More</a>
                    
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                        <a href="app/edit_post.php?id=<?= $post['id'] ?>" class="btn btn-warning">Edit</a>
                        <a href="app/delete_post.php?id=<?= $post['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class='alert alert-warning'>No posts found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

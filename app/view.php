<?php
session_start();
require '../includes/db.php';

// Check if post ID is set in the URL
if (!isset($_GET['id'])) {
    die("Post ID is missing.");
}

$post_id = intval($_GET['id']);

// Fetch the post
$stmt = $pdo->prepare("SELECT posts.*, users.username, categories.name AS category_name 
                       FROM posts 
                       JOIN categories ON posts.category_id = categories.id 
                       JOIN users ON posts.user_id = users.id
                       WHERE posts.id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch();


// Ensure post exists
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
    <title><?= htmlspecialchars($post['title']) ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f9;
    color: #333;
    margin: 0;
    padding: 0;
}

.container {
    width: 80%;
    max-width: 900px;
    margin: 30px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}


h1 {
    font-size: 2.5em;
    color: #333;
    margin-bottom: 20px;
}

/* Category and user info */
p {
    font-size: 1.1em;
    color: #555;
}


.card {
    background-color: #f9f9f9;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-body {
    padding: 20px;
}


.card-title {
    font-weight: bold;
    color: #333;
}

.card-text {
    font-size: 1em;
    color: #555;
}

.text-muted {
    font-size: 0.9em;
    color: #888;
}
textarea {
    width: 100%;
    padding: 10px;
    border-radius: 4px;
    border: 1px solid #ddd;
    font-size: 1em;
}

textarea:focus {
    outline: none;
    border-color: #007bff;
}

button[type="submit"] {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 1.1em;
    cursor: pointer;
    border-radius: 4px;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #0056b3;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-radius: 4px;
    padding: 10px;
}

a {
    color: #007bff;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}


.btn-secondary {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    text-decoration: none;
}

.btn-secondary:hover {
    background-color: #5a6268;
}

</style>


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
            <p>Please <a href="/BlogApp/login.php">login</a> to comment.</p>
        <?php endif; ?>

        <a href="/BlogApp/index.php" class="btn btn-secondary mt-3">Back to Home</a>
    </div>
</body>
</html>

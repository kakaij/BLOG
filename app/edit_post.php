<?php
session_start(); // Start the session
require '../includes/db.php'; // Ensuring the database connection is included

// Redirecting if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if 'id' is provided in the URL (for editing a post)
if (!isset($_GET['id'])) {
    die("Post ID is missing");
}

$post_id = intval($_GET['id']);

// Fetch the post from the database
$stmt = $pdo->prepare("SELECT posts.*, categories.name AS category_name 
                       FROM posts 
                       JOIN categories ON posts.category_id = categories.id 
                       WHERE posts.id = ? AND posts.user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]); // Ensure user can only edit their own posts
$post = $stmt->fetch();


// If no post is found
if (!$post) {
    die("Post not found or you don't have permission to edit this post.");
}

// Predefined categories
$categories = ['adventure', 'romance', 'fantasy', 'history', 'animation'];

// Handle form submission to update post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $category_name = htmlspecialchars($_POST['category_name']); // category selected by user

    // Find the category_id based on the selected category name
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$category_name]);
    $category = $stmt->fetch();
    
    // If category exists, get category_id
    if ($category) {
        $category_id = $category['id'];
    } else {
        // If category doesn't exist, insert it
        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$category_name]);
        $category_id = $pdo->lastInsertId(); // Get the newly inserted category_id
    }

    // Update the post in the database
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, category_id = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$title, $content, $category_id, $post_id, $_SESSION['user_id']]);

    // Redirect to the homepage or the updated post page
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Edit Post</h1>

        <form action="edit.php?id=<?= $post_id ?>" method="POST">
            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" id="title" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
            </div>

            <div class="form-group">
                <label for="content">Post Content</label>
                <textarea id="content" name="content" class="form-control" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="category_name">Category</label>
                <select id="category_name" name="category_name" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>" <?= ($category === $post['category_name']) ? 'selected' : '' ?>>
                            <?= ucfirst($category) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Post</button>
        </form>

        <a href="index.php" class="btn btn-secondary mt-3">Back to Home</a>
    </div>
</body>
</html>

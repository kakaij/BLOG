<?php
session_start();
require '../includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /BlogApp/app/login.php");
    exit;
}

// Ensure post ID is provided
if (!isset($_GET['id'])) {
    die("Post ID is missing.");
}

$post_id = intval($_GET['id']);

// Fetch the post
$stmt = $pdo->prepare("SELECT posts.*, categories.name AS category_name 
                       FROM posts 
                       JOIN categories ON posts.category_id = categories.id 
                       WHERE posts.id = ? AND posts.user_id = ?");
$stmt->execute([$post_id, $_SESSION['user_id']]);
$post = $stmt->fetch();

// If no post is found
if (!$post) {
    die("Post not found or permission denied.");
}

// Fetch all available categories
$categories_stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $categories_stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = intval($_POST['category_id']);

    // Update post
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, category_id = ? WHERE id = ? AND user_id = ?");
    $updated = $stmt->execute([$title, $content, $category_id, $post_id, $_SESSION['user_id']]);

    if ($updated) {
        header("Location: /BlogApp/index.php");
        exit;
    } else {
        echo "Error updating the post.";
    }
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
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= ($category['id'] == $post['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Post</button>
        </form>

        <a href="/BlogApp/index.php" class="btn btn-secondary mt-3">Back to Home</a>
    </div>
</body>
</html>

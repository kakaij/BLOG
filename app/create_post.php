<?php
session_start(); // Start the session
require '../includes/db.php'; // Ensure the database connection is included

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $category_name = htmlspecialchars($_POST['category_name']);
    $user_id = $_SESSION['user_id'];

    try {
        // Check if the category already exists in the database
        $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
        $stmt->execute([$category_name]);
        $category = $stmt->fetch();

        // If category doesn't exist, insert it
        if (!$category) {
            $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
            $stmt->execute([$category_name]);
            // Fetch the newly inserted category ID
            $category_id = $pdo->lastInsertId();
        } else {
            // Use existing category ID
            $category_id = $category['id'];
        }

        // Insert the new post with the selected or newly inserted category
        $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id, category_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $user_id, $category_id]);

        // Redirect to home or other page
        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
        die("Error inserting post: " . $e->getMessage()); // Debugging
    }
}

// Fetch predefined categories for the dropdown
$categories = [
    'adventure', 'romance', 'fantasy', 'history', 'animation'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="my-4">Create New Post</h1>
        
        <!-- Display form for creating new post -->
        <form action="create_post.php" method="POST">
            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" id="title" name="title" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="content">Post Content</label>
                <textarea id="content" name="content" class="form-control" rows="6" required></textarea>
            </div>

            <div class="form-group">
                <label for="category_name">Category</label>
                <select id="category_name" name="category_name" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category) ?>"><?= ucfirst($category) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Create Post</button>
        </form>

        <!-- Back to home button -->
        <a href="index.php" class="btn btn-secondary mt-3">Back to Home</a>
    </div>
</body>
</html>

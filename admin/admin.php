<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "blogapp");

    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Admin Authentication
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../login.php");
        exit();
    }

    // Admin Dashboard Queries
    $user_count = $conn->query("SELECT COUNT(*) as user_count FROM users")->fetch_assoc()['user_count'] ?? 0;
    $post_count = $conn->query("SELECT COUNT(*) as post_count FROM posts")->fetch_assoc()['post_count'] ?? 0;
    $category_count = $conn->query("SELECT COUNT(*) as category_count FROM categories")->fetch_assoc()['category_count'] ?? 0;

    // Fetch all posts with user & category names using prepared statement
    $stmt = $conn->prepare("
        SELECT posts.id, posts.title, users.username, categories.name AS category 
        FROM posts
        JOIN users ON posts.user_id = users.id
        JOIN categories ON posts.category_id = categories.id
    ");
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-4">Admin Dashboard</h1>

        <nav class="mb-4">
            <a href="admin.php" class="btn btn-primary">Dashboard</a>
            <a href="manage_users.php" class="btn btn-secondary">Manage Users</a>
            <a href="manage_categories.php" class="btn btn-secondary">Manage Categories</a>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </nav>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text"><?php echo htmlspecialchars($user_count); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Posts</h5>
                        <p class="card-text"><?php echo htmlspecialchars($post_count); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Categories</h5>
                        <p class="card-text"><?php echo htmlspecialchars($category_count); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <h2>All Posts</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>User</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($post['id']); ?></td>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($post['username']); ?></td>
                        <td><?php echo htmlspecialchars($post['category']); ?></td>
                        <td>
                            <a href="edit_post.php?id=<?php echo urlencode($post['id']); ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_post.php?id=<?php echo urlencode($post['id']); ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>

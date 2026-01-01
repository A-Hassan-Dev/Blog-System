<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';
$message = '';
$message_type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $fileName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $fileName);
        $image = $fileName;
    }
    $stmt = $pdo->prepare("INSERT INTO posts (title, content, image, category_id, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $image, $category_id, $status]);
    $message = "Post added successfully!";
    $message_type = "success";
    echo "<script>window.location.href = 'posts.php';</script>";
    exit();
}
$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="py-4">
        <!-- Page Title -->
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-file-text"></i> Add New Post </h1>
            <a href="posts.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Posts
            </a>
        </div>
        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show" role="alert">
                <?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <!-- Add New Post Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-plus-circle"></i> Create New Blog Post
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Post Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="6" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id']; ?>"><?= $category['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Featured Image</label>
                        <input type="file" class="form-control" name="image">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle"></i> Create Post
                    </button>
                </form>
            </div> <!-- card-body -->
        </div> <!-- card -->
    </div> <!-- py-4 -->
</main>
<?php include 'includes/footer.php'; ?>
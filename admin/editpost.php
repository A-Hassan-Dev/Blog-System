<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';
// Get post ID
$post_id = $_GET['id'] ?? 0;
if (!$post_id) {
    die("Invalid post ID");
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$post) {
    die("Post not found");
}

$message = '';
$message_type = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $title = $_POST['title'];
    $content = $_POST['content'];
    $category_id = $_POST['category_id'];
    $status = $_POST['status'];
    $image = $post['image'];
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $fileName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $fileName);
        $image = $fileName;
    }
   
    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ?, image = ?, category_id = ?, status = ? WHERE id = ?");
    $stmt->execute([$title, $content, $image, $category_id, $status, $post_id]);
    $message = "Post updated successfully!";
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
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2"><i class="bi bi-file-text"></i> Edit Post </h1>
            <a href="posts.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Posts
            </a>
        </div> <!-- d-flex -->
        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show" role="alert">
                <?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <!-- Edit Post Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil-square"></i> Edit Blog Post
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Post Title</label>
                        <input type="text" class="form-control" name="title" value="<?= $post['title']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <textarea class="form-control" name="content" rows="6" required><?= $post['content']; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" required>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id']; ?>" <?php if ($category['id'] == $post['category_id']) echo 'selected'; ?>><?= $category['name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="draft" <?php if ($post['status'] == 'draft') echo 'selected'; ?>>Draft</option>
                            <option value="published" <?php if ($post['status'] == 'published') echo 'selected'; ?>>Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Featured Image (Current: <?= $post['image'] ? $post['image'] : 'None'; ?>)</label>
                        <input type="file" class="form-control" name="image">
                    </div>
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-pencil-square"></i> Update Post
                    </button>
                </form>
            </div> <!-- card-body -->
        </div> <!-- card -->
    </div> <!-- py-4 -->
</main>
<?php include 'includes/footer.php'; ?>
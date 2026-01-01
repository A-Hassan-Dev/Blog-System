<?php
require_once 'config/database.php';
$post_id = $_GET['id'] ?? 0;
// Get post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND status = 'published'");
$stmt->execute([$post_id]);
$post = $stmt->fetch();
if (!$post) {
    die("Post not found!");
}
$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post_id]);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $comment = $_POST['comment'] ?? '';
    if ($name && $email && $comment) {
        $pdo->prepare("INSERT INTO comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)")
            ->execute([$post_id, $name, $email, $comment]);
    }
}
// Get comments
$comments_stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at DESC");
$comments_stmt->execute([$post_id]);
$comments = $comments_stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $post['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">My Blog</a>
            <a href="index.php" class="btn btn-outline-light btn-sm">Home</a>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Post -->
                <article>
                    <h1 class="mb-3"><?= $post['title']; ?></h1>
                    <div class="text-muted mb-4">
                        Posted on: <?= date('F j, Y', strtotime($post['created_at'])); ?> | Views: <?= $post['views']; ?>
                    </div>
                    <?php if (!empty($post['image'])): ?>
                        <img src="uploads/<?= $post['image']; ?>" alt="<?= $post['title']; ?>" class="img-fluid mb-4 rounded">
                    <?php endif; ?>
                    <div class="post-content mb-4">
                        <?= nl2br($post['content']); ?>
                    </div>
                    <a href="index.php" class="btn btn-primary">Back to Posts</a>
                </article>
                
                <div class="mt-5">
                    <h3>Comments</h3>
                    
                    <form method="POST" class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" name="name" class="form-control mb-2" placeholder="Your Name" required>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" class="form-control mb-2" placeholder="Your Email" required>
                            </div>
                        </div>
                        <textarea name="comment" class="form-control mb-2" rows="3" placeholder="Your Comment" required></textarea>
                        <button type="submit" class="btn btn-primary">Post Comment</button>
                    </form>
                    
                    <?php foreach ($comments as $comment): ?>
                        <div class="border p-3 mb-2">
                            <strong><?= $comment['name']; ?></strong>
                            <small class="text-muted"> - <?= $comment['created_at']; ?></small>
                            <p class="mb-0"><?= $comment['comment']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div> 
            </div> 
        </div>
    </div>
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <p class="mb-0">&copy; 2025 My Blog</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
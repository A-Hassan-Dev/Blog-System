<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$total_posts       = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$published_posts   = $pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
$draft_posts       = $pdo->query("SELECT COUNT(*) FROM posts WHERE status='draft'")->fetchColumn();
$total_comments    = $pdo->query("SELECT COUNT(*) FROM comments")->fetchColumn();
$pending_comments  = $pdo->query("SELECT COUNT(*) FROM comments WHERE status='pending'")->fetchColumn();
$total_views       = $pdo->query("SELECT SUM(views) FROM posts")->fetchColumn() ?: 0;


$recent_posts = $pdo->query("SELECT id, title, status, views, created_at FROM posts ORDER BY created_at DESC LIMIT 5")->fetchAll();


$recent_comments = $pdo->query("SELECT c.id, c.name, c.comment, c.status, c.created_at, p.title as post_title 
                                 FROM comments c 
                                 JOIN posts p ON c.post_id = p.id 
                                 ORDER BY c.created_at DESC LIMIT 5")->fetchAll();
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="py-4">

        <!-- Page Title -->
        <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
            <h1 class="h2">
                <i class="bi bi-speedometer2"></i> Dashboard Overview
            </h1>
            <span class="text-muted"><?= date('l, F j, Y'); ?></span>
        </div>

        <!-- 1. Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><?= $total_posts; ?></h4>
                            <p class="mb-0">Total Posts</p>
                        </div>
                        <i class="bi bi-file-text fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><?= $published_posts; ?></h4>
                            <p class="mb-0">Published</p>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-secondary text-white h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><?= $draft_posts; ?></h4>
                            <p class="mb-0">Drafts</p>
                        </div>
                        <i class="bi bi-file-earmark fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><?= $total_comments; ?></h4>
                            <p class="mb-0">Total Comments</p>
                        </div>
                        <i class="bi bi-chat-dots fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><?= $pending_comments; ?></h4>
                            <p class="mb-0">Pending Comments</p>
                        </div>
                        <i class="bi bi-clock fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><?= number_format($total_views); ?></h4>
                            <p class="mb-0">Total Views</p>
                        </div>
                        <i class="bi bi-eye fs-1 opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Latest 5 Posts -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-file-text"></i> Recent Posts</h5>
                        <a href="posts.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Views</th>
                                        <th>Date</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_posts as $p): ?>
                                        <tr>
                                            <td class="text-truncate" style="max-width: 180px;">
                                                <?= htmlspecialchars($p['title']); ?>
                                            </td>
                                            <td>
                                                <?php if ($p['status'] == 'published'): ?>
                                                    <span class="badge bg-success">Published</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Draft</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= $p['views']; ?></td>
                                            <td><?= date('M j', strtotime($p['created_at'])); ?></td>
                                            <td>
                                                <a href="editpost.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Latest 5 Comments -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Recent Comments</h5>
                        <a href="comments.php" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Author</th>
                                        <th>Comment</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_comments as $c): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($c['name']); ?></td>
                                            <td class="text-truncate" style="max-width: 150px;">
                                                <?= htmlspecialchars(substr($c['comment'], 0, 50)) . '...'; ?>
                                            </td>
                                            <td>
                                                <?php if ($c['status'] == 'approved'): ?>
                                                    <span class="badge bg-success">Approved</span>
                                                <?php elseif ($c['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Rejected</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($c['status'] == 'pending'): ?>
                                                    <a href="comments.php?action=approve&id=<?= $c['id']; ?>" class="btn btn-sm btn-success" title="Approve">
                                                        <i class="bi bi-check"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
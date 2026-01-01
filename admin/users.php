<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
require_once '../config/database.php';
include 'includes/header.php';
include 'includes/sidebar.php';

$message = '';
$message_type = '';


session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php");
    exit();
}
if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied. Admins only.");
}

// Handle Add User
if (isset($_POST['add_user'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'] ?? 'author';

    if (empty($name) || empty($email) || empty($password)) {
        $message = "All fields are required!";
        $message_type = "danger";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $hashed, $role]);
            $message = "User added successfully!";
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error: Email already exists or invalid data!";
            $message_type = "danger";
        }
    }
}

// Handle Update User
if (isset($_POST['update_user'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if (empty($name) || empty($email)) {
        $message = "Name and Email are required!";
        $message_type = "danger";
    } else {
        if (!empty($password)) {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET name=?, email=?, password=?, role=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $email, $hashed, $role, $id]);
        } else {
            $sql = "UPDATE users SET name=?, email=?, role=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $email, $role, $id]);
        }
        $message = "User updated successfully!";
        $message_type = "success";
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id !== 1) { 
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        $message = "User deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Cannot delete the main admin!";
        $message_type = "danger";
    }
}


$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();


$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_user = $stmt->fetch();
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
            <h1 class="h2"><i class="bi bi-people"></i> Users Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus"></i> Add New User
            </button>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $message_type; ?> alert-dismissible fade show">
                <?= $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">All Users</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id']; ?></td>
                                <td><?= htmlspecialchars($user['name']); ?></td>
                                <td><?= htmlspecialchars($user['email']); ?></td>
                                <td>
                                    <span class="badge <?= $user['role']=='admin' ? 'bg-danger' : 'bg-info'; ?>">
                                        <?= ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td><?= date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                            onclick="fillEditModal(<?= $user['id']; ?>, '<?= addslashes($user['name']); ?>', '<?= addslashes($user['email']); ?>', '<?= $user['role']; ?>')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <a href="users.php?delete=<?= $user['id']; ?>" class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="bi bi-trash"></i>
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
</main>


<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="author">Author</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" name="name" id="edit_name" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">New Password (leave blank to keep current)</label><input type="password" name="password" class="form-control"></div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="edit_role" class="form-select">
                            <option value="author">Author</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_user" class="btn btn-warning">Update User</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Fill Edit Modal with user data
function fillEditModal(id, name, email, role) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;
}
</script>

<?php include 'includes/footer.php'; ?>
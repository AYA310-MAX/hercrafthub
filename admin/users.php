<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.php');
  exit;
}

// ── Search ──
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['role'])   ? $_GET['role']         : '';

$sql = "SELECT id, full_name, email, role, is_active, created_at FROM users WHERE 1=1";
if ($search) $sql .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%')";
if ($filter) $sql .= " AND role = '$filter'";
$sql .= " ORDER BY created_at DESC";
$users = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/jpeg" href="../images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Users – HerCraft Hub Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="css/admin.css?v=<?= filemtime('css/admin.css') ?>">
</head>
<body>

<?php include 'includes/admin_navbar.php'; ?>

<div class="admin-wrapper">
  <?php include 'includes/admin_sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-header">
      <h4 class="mb-0">Manage Users</h4>
      <a href="create_user.php" class="btn btn-primary btn-sm">+ Add New User</a>
    </div>

    <!-- ── Search and Filter ── -->
    <div class="admin-card mb-4">
      <form method="GET" class="row g-3">
        <div class="col-md-6">
          <input type="text" name="search" class="form-control"
                 placeholder="Search by name or email..."
                 value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
          <select name="role" class="form-select">
            <option value="">All Roles</option>
            <option value="admin"  <?= $filter=='admin' ?'selected':'' ?>>Admin</option>
            <option value="seller" <?= $filter=='seller'?'selected':'' ?>>Seller</option>
            <option value="buyer"  <?= $filter=='buyer' ?'selected':'' ?>>Buyer</option>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100">Search</button>
          <a href="users.php" class="btn btn-outline-primary w-100">Clear</a>
        </div>
      </form>
    </div>

    <!-- ── Users Table ── -->
    <div class="admin-card">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th>Joined</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($u = $users->fetch_assoc()): ?>
            <tr>
              <td><?= $u['id'] ?></td>
              <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <span class="role-badge role-<?= $u['role'] ?>">
                  <?= ucfirst($u['role']) ?>
                </span>
              </td>
              <td>
                <span class="status-badge <?= $u['is_active']?'active':'inactive' ?>">
                  <?= $u['is_active'] ? 'Active' : 'Suspended' ?>
                </span>
              </td>
              <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
              <td>
                <div class="d-flex gap-1">
                  <a href="edit_user.php?id=<?= $u['id'] ?>"
                     class="btn btn-sm btn-outline-primary">Edit</a>
                  <a href="toggle_user.php?id=<?= $u['id'] ?>"
                     class="btn btn-sm btn-outline-warning">
                     <?= $u['is_active'] ? 'Suspend' : 'Activate' ?>
                  </a>
                  <?php if($u['id'] != $_SESSION['user_id']): ?>
                  <a href="delete_user.php?id=<?= $u['id'] ?>"
                     class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Are you sure you want to delete this user?')">
                     Delete
                  </a>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

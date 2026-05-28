<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.php'); exit;
}

$id   = (int)$_GET['id'];
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();

if (!$user) {
  header('Location: users.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $full_name = trim($_POST['full_name']);
  $email     = trim($_POST['email']);
  $role      = $_POST['role'];

  $stmt = $conn->prepare(
    "UPDATE users SET full_name=?, email=?, role=? WHERE id=?"
  );
  $stmt->bind_param("sssi", $full_name, $email, $role, $id);
  $stmt->execute();
  $stmt->close();

  $_SESSION['success'] = "User updated successfully!";
  header('Location: users.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit User – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<?php include 'includes/admin_navbar.php'; ?>
<div class="admin-wrapper">
  <?php include 'includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-header">
      <h4>Edit User</h4>
      <a href="users.php" class="btn btn-outline-primary btn-sm">← Back</a>
    </div>
    <div class="admin-card" style="max-width:560px;">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="full_name" class="form-control"
                 value="<?= htmlspecialchars($user['full_name']) ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control"
                 value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="mb-4">
          <label class="form-label">Role</label>
          <select name="role" class="form-select">
            <option value="buyer"  <?= $user['role']=='buyer' ?'selected':'' ?>>Buyer</option>
            <option value="seller" <?= $user['role']=='seller'?'selected':'' ?>>Seller</option>
            <option value="admin"  <?= $user['role']=='admin' ?'selected':'' ?>>Admin</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Save Changes</button>
      </form>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $full_name = trim($_POST['full_name']);
  $email     = trim($_POST['email']);
  $password  = password_hash($_POST['password'], PASSWORD_BCRYPT);
  $role      = $_POST['role'];

  $stmt = $conn->prepare(
    "INSERT INTO users (full_name, email, password, role, is_verified) VALUES (?,?,?,?,1)"
  );
  $stmt->bind_param("ssss", $full_name, $email, $password, $role);
  $stmt->execute();
  $stmt->close();

  $_SESSION['success'] = "User created successfully!";
  header('Location: users.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create User – Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
<?php include 'includes/admin_navbar.php'; ?>
<div class="admin-wrapper">
  <?php include 'includes/admin_sidebar.php'; ?>
  <main class="admin-main">
    <div class="admin-header">
      <h4>Create New User</h4>
      <a href="users.php" class="btn btn-outline-primary btn-sm">← Back</a>
    </div>
    <div class="admin-card" style="max-width:560px;">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" name="full_name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" 
                 minlength="6" required>
        </div>
        <div class="mb-4">
          <label class="form-label">Role</label>
          <select name="role" class="form-select" required>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary w-100">Create User</button>
      </form>
    </div>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
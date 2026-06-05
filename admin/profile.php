<?php
session_start();
require 'config/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php'); exit;
}

$id   = $_SESSION['user_id'];
$user = $conn->query("SELECT * FROM users WHERE id=$id")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Profile – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container my-5" style="max-width:680px;">

  <a href="javascript:history.back()" class="back-btn">
    <i class="ti ti-arrow-left"></i> Back
  </a>

  <?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <div class="card p-4 p-md-5">

    <!-- Avatar and name -->
    <div class="text-center mb-4">
      <div class="user-avatar mx-auto mb-3"
           style="width:80px;height:80px;font-size:2rem;border-width:3px;">
        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
      </div>
      <h2 style="font-family:var(--font-head);color:var(--purple);">
        <?= htmlspecialchars($user['full_name']) ?>
      </h2>
      <span class="role-badge role-<?= $user['role'] ?> mt-1 d-inline-block">
        <?= ucfirst($user['role']) ?>
      </span>
    </div>

    <form action="php/update_profile.php" method="POST">

      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="full_name" class="form-control"
               value="<?= htmlspecialchars($user['full_name']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-control"
               value="<?= htmlspecialchars($user['email']) ?>" disabled>
        <small class="text-muted">Email cannot be changed.</small>
      </div>

      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control"
               placeholder="e.g. Johannesburg, Gauteng"
               value="<?= htmlspecialchars($user['location'] ?? '') ?>">
      </div>

      <div class="mb-4">
        <label class="form-label">Bio</label>
        <textarea name="bio" class="form-control" rows="3"
                  placeholder="Tell buyers a little about yourself..."
        ><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary w-100">
        Save Changes
      </button>

    </form>

    <hr class="my-4">

    <div class="text-center">
      <p class="text-muted small">
        Member since <?= date('F Y', strtotime($user['created_at'])) ?>
      </p>
      <a href="logout.php" class="btn btn-outline-secondary btn-sm mt-2">
        Logout
      </a>
    </div>

  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<script src="js/main.js"></script>
</body>
</html>
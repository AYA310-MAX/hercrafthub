<nav class="admin-nav">
  <div class="d-flex align-items-center gap-3">
    <a href="index.php" class="admin-brand">✦ HerCraft<span>Hub</span> Admin</a>
  </div>
  <div class="d-flex align-items-center gap-3">
    <span class="text-muted small">
      Logged in as <strong><?= $_SESSION['full_name'] ?></strong>
    </span>
    <a href="../logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
  </div>
</nav>
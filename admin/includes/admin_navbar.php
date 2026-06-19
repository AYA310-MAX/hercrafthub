<nav class="admin-nav">
  <div class="d-flex align-items-center gap-3">
    <span class="text-muted small d-none d-md-inline-block">
      Control Panel &middot; Real-time system insights
    </span>
  </div>
  <div class="d-flex align-items-center gap-3">
    <span class="text-muted small">
      Logged in as <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong>
    </span>
    <a href="../logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
  </div>
</nav>
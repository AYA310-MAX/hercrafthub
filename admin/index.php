<?php
session_start();
require '../config/db.php';

// ── RBAC: Admin only ──
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.php');
  exit;
}

// ── Fetch stats ──
$total_users    = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];
$total_sellers  = $conn->query("SELECT COUNT(*) FROM users WHERE role='seller'")->fetch_row()[0];
$total_buyers   = $conn->query("SELECT COUNT(*) FROM users WHERE role='buyer'")->fetch_row()[0];
$total_listings = $conn->query("SELECT COUNT(*) FROM products")->fetch_row()[0];
$active_listings= $conn->query("SELECT COUNT(*) FROM products WHERE is_active=1")->fetch_row()[0];
$sold_listings  = $conn->query("SELECT COUNT(*) FROM products WHERE is_sold=1")->fetch_row()[0];

// ── Fetch recent users ──
$recent_users = $conn->query(
  "SELECT id, full_name, email, role, is_active, created_at 
   FROM users ORDER BY created_at DESC LIMIT 5"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/jpeg" href="../images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard – HerCraft Hub</title>
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
      <h4 class="mb-0">Dashboard Overview</h4>
      <span class="text-muted small">Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?> ✦</span>
    </div>

    <!-- ── Stats Cards ── -->
    <div class="row g-4 mb-4">
      <?php
        $stats = [
          ["Total Users",    $total_users,    "ti ti-users", "purple"],
          ["Sellers",        $total_sellers,  "ti ti-building-store", "pink"],
          ["Buyers",         $total_buyers,   "ti ti-shopping-cart", "teal"],
          ["Total Listings", $total_listings, "ti ti-package", "amber"],
          ["Active Listings",$active_listings,"ti ti-circle-check", "green"],
          ["Sold Items",     $sold_listings,  "ti ti-cash", "blue"],
        ];
        foreach($stats as $s):
      ?>
      <div class="col-sm-6 col-xl-4">
        <div class="stat-card">
          <div class="stat-icon"><i class="<?= $s[2] ?>"></i></div>
          <div>
            <div class="stat-number"><?= $s[1] ?></div>
            <div class="stat-label"><?= $s[0] ?></div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ── Recent Users Table ── -->
    <div class="admin-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0" style="color:var(--purple);">Recent Users</h6>
        <a href="users.php" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
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
            <?php while($u = $recent_users->fetch_assoc()): ?>
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
                <span class="status-badge <?= $u['is_active'] ? 'active' : 'inactive' ?>">
                  <?= $u['is_active'] ? 'Active' : 'Suspended' ?>
                </span>
              </td>
              <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
              <td>
                <a href="edit_user.php?id=<?= $u['id'] ?>" 
                   class="btn btn-sm btn-outline-primary">Edit</a>
                <a href="delete_user.php?id=<?= $u['id'] ?>" 
                   class="btn btn-sm btn-outline-danger"
                   onclick="return confirm('Delete this user?')">Delete</a>
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
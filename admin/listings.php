<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.php'); exit;
}

// ── Delete listing ──
if (isset($_GET['delete'])) {
  $del_id = (int)$_GET['delete'];
  $conn->query("DELETE FROM products WHERE id=$del_id");
  $_SESSION['success'] = "Listing deleted.";
  header('Location: listings.php'); exit;
}

// ── Toggle active/inactive ──
if (isset($_GET['toggle'])) {
  $tog_id  = (int)$_GET['toggle'];
  $current = $conn->query("SELECT is_active FROM products WHERE id=$tog_id")->fetch_row()[0];
  $new     = $current ? 0 : 1;
  $conn->query("UPDATE products SET is_active=$new WHERE id=$tog_id");
  header('Location: listings.php'); exit;
}

// ── Search & filter ──
$search   = isset($_GET['search'])   ? trim($_GET['search'])   : '';
$cat_filter = isset($_GET['category']) ? $_GET['category']     : '';

$sql = "SELECT p.*, u.full_name AS seller_name, c.name AS category_name
        FROM products p
        LEFT JOIN users u      ON p.seller_id   = u.id
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE 1=1";

if ($search)     $sql .= " AND (p.title LIKE '%$search%' OR u.full_name LIKE '%$search%')";
if ($cat_filter) $sql .= " AND c.name = '$cat_filter'";
$sql .= " ORDER BY p.created_at DESC";

$listings   = $conn->query($sql);
$categories = $conn->query("SELECT name FROM categories");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Listings – HerCraft Hub Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<?php include 'includes/admin_navbar.php'; ?>

<div class="admin-wrapper">
  <?php include 'includes/admin_sidebar.php'; ?>

  <main class="admin-main">

    <div class="admin-header">
      <h4 class="mb-0">Manage Listings</h4>
    </div>

    <!-- ── Success / Error Messages ── -->
    <?php if(isset($_SESSION['success'])): ?>
      <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <!-- ── Search and Filter ── -->
    <div class="admin-card mb-4">
      <form method="GET" class="row g-3">
        <div class="col-md-6">
          <input type="text" name="search" class="form-control"
                 placeholder="Search by title or seller name..."
                 value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-3">
          <select name="category" class="form-select">
            <option value="">All Categories</option>
            <?php while($cat = $categories->fetch_assoc()): ?>
              <option value="<?= $cat['name'] ?>"
                <?= $cat_filter == $cat['name'] ? 'selected' : '' ?>>
                <?= $cat['name'] ?>
              </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100">Search</button>
          <a href="listings.php" class="btn btn-outline-primary w-100">Clear</a>
        </div>
      </form>
    </div>

    <!-- ── Listings Table ── -->
    <div class="admin-card">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Image</th>
              <th>Title</th>
              <th>Seller</th>
              <th>Category</th>
              <th>Price</th>
              <th>Status</th>
              <th>Listed</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($l = $listings->fetch_assoc()): ?>
            <tr>
              <td><?= $l['id'] ?></td>
              <td>
                <?php if($l['image']): ?>
                  <img src="../uploads/<?= $l['image'] ?>"
                       style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                <?php else: ?>
                  <div style="width:50px;height:50px;border-radius:8px;
                              background:var(--bg);display:flex;align-items:center;
                              justify-content:center;font-size:1.2rem;">📦</div>
                <?php endif; ?>
              </td>
              <td><strong><?= htmlspecialchars($l['title']) ?></strong></td>
              <td><?= htmlspecialchars($l['seller_name']) ?></td>
              <td>
                <span class="role-badge role-buyer">
                  <?= $l['category_name'] ?? 'Uncategorised' ?>
                </span>
              </td>
              <td style="color:var(--purple);font-weight:700;">
                R<?= number_format($l['price'], 2) ?>
              </td>
              <td>
                <span class="status-badge <?= $l['is_active'] ? 'active' : 'inactive' ?>">
                  <?= $l['is_active'] ? 'Active' : 'Hidden' ?>
                </span>
                <?php if($l['is_sold']): ?>
                  <span class="status-badge active ms-1">Sold</span>
                <?php endif; ?>
              </td>
              <td><?= date('d M Y', strtotime($l['created_at'])) ?></td>
              <td>
                <div class="d-flex gap-1">
                  <a href="../listing.php?id=<?= $l['id'] ?>" target="_blank"
                     class="btn btn-sm btn-outline-primary">View</a>
                  <a href="listings.php?toggle=<?= $l['id'] ?>"
                     class="btn btn-sm btn-outline-warning">
                     <?= $l['is_active'] ? 'Hide' : 'Show' ?>
                  </a>
                  <a href="listings.php?delete=<?= $l['id'] ?>"
                     class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Delete this listing permanently?')">
                     Delete
                  </a>
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
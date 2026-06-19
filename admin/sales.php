<?php
session_start();
require '../config/db.php';
require '../includes/helpers.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.php');
  exit;
}

$sales_query = $conn->query("
  SELECT s.id AS sale_id, s.quantity, s.item_total, s.created_at,
         p.title AS product_title,
         b.id AS buyer_uid, b.full_name AS buyer_name,
         se.id AS seller_uid, se.full_name AS seller_name
  FROM sales s
  JOIN products p ON s.product_id = p.id
  JOIN users b ON s.buyer_id = b.id
  JOIN users se ON s.seller_id = se.id
  ORDER BY s.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="icon" type="image/jpeg" href="../images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sales Tracking – Admin</title>
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
      <h4 class="mb-0">Sales Tracking</h4>
      <span class="text-muted small">Monitor all platform transactions</span>
    </div>

    <div class="admin-card">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead>
            <tr>
              <th>Sale ID</th>
              <th>Date</th>
              <th>Product</th>
              <th>Buyer (Unique ID)</th>
              <th>Seller (Unique ID)</th>
              <th>Quantity</th>
              <th>Total Amount</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($sales_query->num_rows > 0): ?>
              <?php while($s = $sales_query->fetch_assoc()): ?>
              <tr>
                <td>#<?= $s['sale_id'] ?></td>
                <td><?= date('d M Y, H:i', strtotime($s['created_at'])) ?></td>
                <td><strong><?= htmlspecialchars($s['product_title']) ?></strong></td>
                <td><?= htmlspecialchars($s['buyer_name']) ?> <span class="badge bg-secondary ms-1">ID: <?= $s['buyer_uid'] ?></span></td>
                <td><?= htmlspecialchars($s['seller_name']) ?> <span class="badge bg-secondary ms-1">ID: <?= $s['seller_uid'] ?></span></td>
                <td><?= $s['quantity'] ?></td>
                <td class="fw-bold text-success"><?= format_price($s['item_total']) ?></td>
              </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center py-4 text-muted">No sales recorded yet.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

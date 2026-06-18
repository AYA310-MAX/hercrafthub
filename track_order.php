<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';
require_once 'includes/auth.php';

require_buyer();

$sale_id = isset($_GET['sale_id']) ? (int) $_GET['sale_id'] : 0;
$buyer_id = (int) $_SESSION['user_id'];

if ($sale_id <= 0) {
    $_SESSION['error'] = 'Order not found.';
    redirect_to('dashboard.php');
}

$order = db_fetch_one(
    $conn,
    'SELECT s.id, s.tracking_status, s.total_amount, s.delivery_address, s.created_at, 
            p.title, p.image, u.full_name AS seller_name
     FROM sales s
     INNER JOIN products p ON s.product_id = p.id
     INNER JOIN users u ON s.seller_id = u.id
     WHERE s.id = ? AND s.buyer_id = ?
     LIMIT 1',
    'ii',
    [$sale_id, $buyer_id]
);

if ($order === null) {
    $_SESSION['error'] = 'Order not found or access denied.';
    redirect_to('dashboard.php');
}

$status = $order['tracking_status'];
$statuses = ['Processing', 'Shipped', 'In Transit', 'Delivered'];
$current_index = array_search($status, $statuses);
if ($current_index === false) $current_index = 0;

?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Track Order – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    .tracking-timeline {
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      margin: 40px 0;
      padding: 0 20px;
    }
    .tracking-timeline::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 30px;
      right: 30px;
      height: 4px;
      background: var(--border);
      z-index: 1;
      transform: translateY(-50%);
    }
    .tracking-step {
      position: relative;
      z-index: 2;
      text-align: center;
      width: 80px;
    }
    .tracking-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: var(--cream-dark);
      border: 3px solid var(--border);
      color: var(--text-muted);
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 10px;
      font-size: 1.2rem;
      transition: var(--transition);
    }
    .tracking-step.completed .tracking-icon {
      background: var(--purple);
      border-color: var(--purple);
      color: white;
    }
    .tracking-step.active .tracking-icon {
      background: white;
      border-color: var(--pink);
      color: var(--pink);
      box-shadow: 0 0 0 4px rgba(122, 59, 94, 0.1);
    }
    .tracking-label {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }
    .tracking-step.completed .tracking-label,
    .tracking-step.active .tracking-label {
      color: var(--text-dark);
    }
  </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
  <a href="dashboard.php" class="back-btn">
    <i class="ti ti-arrow-left"></i> Back to dashboard
  </a>
</div>

<section class="container my-4">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      <div class="card p-5">
        <h3 class="listing-title mb-4 text-center">Track Your Order</h3>
        
        <div class="tracking-timeline">
          <?php foreach ($statuses as $index => $s): ?>
            <?php
              $is_completed = $index < $current_index;
              $is_active = $index === $current_index;
              $class = $is_completed ? 'completed' : ($is_active ? 'active' : '');
              $icons = ['box', 'truck-loading', 'truck', 'home-check'];
            ?>
            <div class="tracking-step <?= $class ?>">
              <div class="tracking-icon">
                <i class="ti ti-<?= $icons[$index] ?>"></i>
              </div>
              <div class="tracking-label"><?= $s ?></div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="alert alert-secondary mt-4 mb-4 text-center">
          <h5 class="mb-1">Current Status: <strong class="text-primary"><?= htmlspecialchars($status) ?></strong></h5>
          <p class="mb-0 small text-muted">Last updated by <?= htmlspecialchars($order['seller_name']) ?></p>
        </div>

        <hr>

        <h5 class="listing-title mt-4 mb-3">Order Details</h5>
        <div class="d-flex gap-3 mb-3">
          <img src="<?= htmlspecialchars(product_image_src($order['image'])) ?>"
               alt="<?= htmlspecialchars($order['title']) ?>"
               class="dashboard-thumb">
          <div>
            <h6 class="mt-1 listing-title"><?= htmlspecialchars($order['title']) ?></h6>
            <p class="text-muted small mb-1">Sold by <?= htmlspecialchars($order['seller_name']) ?></p>
            <p class="listing-price mb-0"><?= format_price($order['total_amount']) ?></p>
          </div>
        </div>
        
        <div class="row mt-4">
          <div class="col-sm-6">
            <p class="mb-1 text-muted small text-uppercase">Order Date</p>
            <p><strong><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></strong></p>
          </div>
          <div class="col-sm-6">
            <p class="mb-1 text-muted small text-uppercase">Delivery Address</p>
            <p><strong><?= htmlspecialchars($order['delivery_address']) ?></strong></p>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

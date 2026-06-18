<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';
require_once 'includes/auth.php';

require_login();

$user_id = (int) $_SESSION['user_id'];
$role    = $_SESSION['role'];

if ($role === 'admin') {
    header('Location: admin/index.php');
    exit;
}

$account = db_fetch_one(
    $conn,
    'SELECT full_name, email, role, is_active, is_verified, created_at FROM users WHERE id = ? LIMIT 1',
    'i',
    [$user_id]
);

if ($account === null) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$active_listings = 0;
$sold_listings   = 0;
$total_earnings  = 0.0;
$seller_products = [];
$sales_history    = [];
$purchase_history = [];
$wishlist_items  = [];
$recent_activity = [];
$wishlist_count  = 0;
$message_count   = 0;

if ($role === 'seller') {
    $stats = db_fetch_one(
        $conn,
        'SELECT
            SUM(CASE WHEN is_active = 1 AND is_sold = 0 AND quantity > 0 THEN 1 ELSE 0 END) AS active_count,
            SUM(CASE WHEN is_sold = 1 OR quantity = 0 THEN 1 ELSE 0 END) AS sold_count
         FROM products WHERE seller_id = ?',
        'i',
        [$user_id]
    );
    $active_listings = (int) ($stats['active_count'] ?? 0);
    $sold_listings   = (int) ($stats['sold_count'] ?? 0);

    $earnings_row = db_fetch_one(
        $conn,
        'SELECT COALESCE(SUM(item_total), 0) AS total FROM sales WHERE seller_id = ?',
        'i',
        [$user_id]
    );
    $total_earnings = (float) ($earnings_row['total'] ?? 0);

    $seller_products = db_fetch_all(
        $conn,
        'SELECT p.id, p.title, p.price, p.quantity, p.is_active, p.is_sold, p.created_at, c.name AS category_name
         FROM products p
         INNER JOIN categories c ON p.category_id = c.id
         WHERE p.seller_id = ?
         ORDER BY p.created_at DESC',
        'i',
        [$user_id]
    );

    $sales_history = db_fetch_all(
        $conn,
        'SELECT s.id AS sale_id, s.item_total, s.created_at, s.tracking_status, p.title, p.id AS product_id, u.full_name AS buyer_name
         FROM sales s
         INNER JOIN products p ON s.product_id = p.id
         INNER JOIN users u ON s.buyer_id = u.id
         WHERE s.seller_id = ?
         ORDER BY s.created_at DESC
         LIMIT 10',
        'i',
        [$user_id]
    );
}

if ($role === 'buyer') {
    $purchase_history = db_fetch_all(
        $conn,
        'SELECT s.id AS sale_id, s.total_amount, s.delivery_address, s.delivery_fee, s.charity_donation,
                s.item_total, s.created_at, s.tracking_status, p.title, p.id AS product_id, u.full_name AS seller_name
         FROM sales s
         INNER JOIN products p ON s.product_id = p.id
         INNER JOIN users u ON s.seller_id = u.id
         WHERE s.buyer_id = ?
         ORDER BY s.created_at DESC
         LIMIT 10',
        'i',
        [$user_id]
    );
}

if ($role === 'buyer' || $role === 'seller') {
    $wishlist_items = db_fetch_all(
        $conn,
        'SELECT w.id AS wishlist_id, w.created_at, p.id, p.title, p.price, p.image, c.name AS category_name
         FROM wishlists w
         INNER JOIN products p ON w.product_id = p.id
         INNER JOIN categories c ON p.category_id = c.id
         WHERE w.user_id = ? AND p.is_active = 1 AND p.is_sold = 0 AND p.quantity > 0
         ORDER BY w.created_at DESC',
        'i',
        [$user_id]
    );
    $wishlist_count = count($wishlist_items);

    $msg_stats = db_fetch_one(
        $conn,
        'SELECT
            SUM(CASE WHEN receiver_id = ? THEN 1 ELSE 0 END) AS received_count,
            SUM(CASE WHEN sender_id = ? THEN 1 ELSE 0 END) AS sent_count
         FROM messages
         WHERE receiver_id = ? OR sender_id = ?',
        'iiii',
        [$user_id, $user_id, $user_id, $user_id]
    );
    $message_count = (int) (($msg_stats['received_count'] ?? 0) + ($msg_stats['sent_count'] ?? 0));

    $recent_activity = db_fetch_all(
        $conn,
        'SELECT activity_type, activity_date, title, product_id FROM (
            SELECT "wishlist" AS activity_type, w.created_at AS activity_date, p.title, p.id AS product_id
            FROM wishlists w
            INNER JOIN products p ON w.product_id = p.id
            WHERE w.user_id = ?
            UNION ALL
            SELECT "message" AS activity_type, m.created_at AS activity_date,
                   COALESCE(p.title, "General enquiry") AS title, m.product_id
            FROM messages m
            LEFT JOIN products p ON m.product_id = p.id
            WHERE m.sender_id = ? OR m.receiver_id = ?
         ) AS combined
         ORDER BY activity_date DESC
         LIMIT 8',
        'iii',
        [$user_id, $user_id, $user_id]
    );
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="page-header" style="padding:40px 0;">
  <div class="container">
    <h1>My Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($account['full_name']) ?> &middot;
      <a href="profile.php" class="text-white-50">Edit Profile</a>
    </p>
  </div>
</section>

<div class="container my-5">
  <?= render_flash_messages() ?>

  <div class="alert alert-secondary small py-2 mb-4">
    <i class="ti ti-clock me-1"></i> Dashboard sessions expire after 30 minutes of inactivity for your security.
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card dashboard-stat p-4">
        <small class="text-muted text-uppercase">Account Role</small>
        <h4 class="listing-title mt-1"><?= ucfirst(htmlspecialchars($account['role'])) ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card dashboard-stat p-4">
        <small class="text-muted text-uppercase">Account Status</small>
        <h4 class="listing-title mt-1">
          <?= (int) $account['is_active'] ? 'Active' : 'Suspended' ?>
        </h4>
      </div>
    </div>
    <?php if ($role === 'seller'): ?>
    <div class="col-md-3">
      <div class="card dashboard-stat p-4">
        <small class="text-muted text-uppercase">Active Listings</small>
        <h4 class="listing-title mt-1"><?= $active_listings ?></h4>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card dashboard-stat p-4" style="border-left-color:var(--pink);">
        <small class="text-muted text-uppercase">Total Earnings</small>
        <h4 class="listing-price mt-1"><?= format_price($total_earnings) ?></h4>
      </div>
    </div>
    <?php else: ?>
    <div class="col-md-3">
      <div class="card dashboard-stat p-4">
        <small class="text-muted text-uppercase">Saved Items</small>
        <h4 class="listing-title mt-1"><?= $wishlist_count ?></h4>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <?php if ($role === 'seller'): ?>
  <div class="card p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="listing-title mb-0">My Listings</h5>
      <a href="sell.php" class="btn btn-primary btn-sm">
        <i class="ti ti-plus me-1"></i>New Listing
      </a>
    </div>

    <?php if (count($seller_products) === 0): ?>
    <p class="text-muted mb-0">You have not listed any items yet.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Visibility</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($seller_products as $product): ?>
          <tr>
            <td><strong><?= htmlspecialchars($product['title']) ?></strong></td>
            <td><?= htmlspecialchars($product['category_name']) ?></td>
            <td><?= format_price($product['price']) ?></td>
            <td><?= (int) $product['quantity'] ?></td>
            <td>
              <span class="status-badge <?= (int) $product['is_active'] ? 'active' : 'inactive' ?>">
                <?= (int) $product['is_active'] ? 'Visible' : 'Hidden' ?>
              </span>
            </td>
            <td>
              <?php if ((int) $product['is_sold'] || (int) $product['quantity'] < 1): ?>
              <span class="status-badge inactive">Sold Out</span>
              <?php else: ?>
              <span class="status-badge active">Available</span>
              <?php endif; ?>
            </td>
            <td class="d-flex gap-1 flex-wrap">
              <a href="edit_listing.php?id=<?= (int) $product['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
              <a href="php/listing_manage.php?action=toggle&id=<?= (int) $product['id'] ?>"
                 class="btn btn-sm btn-outline-secondary">
                <?= (int) $product['is_active'] ? 'Hide' : 'Show' ?>
              </a>
              <a href="php/listing_manage.php?action=delete&id=<?= (int) $product['id'] ?>"
                 class="btn btn-sm btn-outline-danger"
                 onclick="return confirm('Delete this listing permanently?')">Delete</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

    <div class="mt-3 text-muted small">
      Sold out listings: <?= $sold_listings ?>
    </div>
  </div>

  <?php if (count($sales_history) > 0): ?>
  <div class="card p-4 mb-4">
    <h5 class="listing-title mb-3">Recent Sales</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Item</th>
            <th>Buyer</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Tracking</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($sales_history as $sale): ?>
          <tr>
            <td>
              <a href="listing.php?id=<?= (int) $sale['product_id'] ?>">
                <?= htmlspecialchars($sale['title']) ?>
              </a>
            </td>
            <td><?= htmlspecialchars($sale['buyer_name']) ?></td>
            <td class="listing-price"><?= format_price($sale['item_total']) ?></td>
            <td><?= date('d M Y', strtotime($sale['created_at'])) ?></td>
            <td>
              <form action="php/update_tracking_action.php" method="POST" class="d-flex align-items-center gap-2">
                <input type="hidden" name="sale_id" value="<?= (int) $sale['sale_id'] ?>">
                <select name="tracking_status" class="form-select form-select-sm" style="width: 130px;">
                  <option value="Processing" <?= $sale['tracking_status'] === 'Processing' ? 'selected' : '' ?>>Processing</option>
                  <option value="Shipped" <?= $sale['tracking_status'] === 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                  <option value="In Transit" <?= $sale['tracking_status'] === 'In Transit' ? 'selected' : '' ?>>In Transit</option>
                  <option value="Delivered" <?= $sale['tracking_status'] === 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary">Update</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>
  <?php endif; ?>

  <?php if ($role === 'buyer' && count($purchase_history) > 0): ?>
  <div class="card p-4 mb-4">
    <h5 class="listing-title mb-3">My Purchases</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Item</th>
            <th>Seller</th>
            <th>Deliver to</th>
            <th>Total paid</th>
            <th>Date</th>
            <th>Tracking</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($purchase_history as $order): ?>
          <tr>
            <td><?= htmlspecialchars($order['title']) ?></td>
            <td><?= htmlspecialchars($order['seller_name']) ?></td>
            <td class="small"><?= htmlspecialchars($order['delivery_address']) ?></td>
            <td class="listing-price"><?= format_price($order['total_amount']) ?></td>
            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
            <td>
              <a href="track_order.php?sale_id=<?= (int) $order['sale_id'] ?>" class="btn btn-sm btn-outline-primary">Track Order</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($role === 'buyer' || ($role === 'seller' && count($wishlist_items) > 0)): ?>
  <div class="card p-4 mb-4">
    <h5 class="listing-title mb-3">My Wishlist</h5>

    <?php if (count($wishlist_items) === 0): ?>
    <p class="text-muted mb-0">You have not saved any items yet.</p>
    <?php else: ?>
    <div class="row g-3">
      <?php foreach ($wishlist_items as $item): ?>
      <div class="col-md-6">
        <div class="card p-3 h-100">
          <div class="d-flex gap-3">
            <img src="<?= htmlspecialchars(product_image_src($item['image'])) ?>"
                 alt="<?= htmlspecialchars($item['title']) ?>"
                 class="dashboard-thumb">
            <div class="flex-grow-1">
              <span class="badge-category"><?= htmlspecialchars($item['category_name']) ?></span>
              <h6 class="mt-1 listing-title"><?= htmlspecialchars($item['title']) ?></h6>
              <p class="listing-price mb-2"><?= format_price($item['price']) ?></p>
              <div class="d-flex gap-2">
                <a href="listing.php?id=<?= (int) $item['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                <a href="php/wishlist_action.php?action=remove&product_id=<?= (int) $item['id'] ?>&redirect=dashboard"
                   class="btn btn-sm btn-outline-danger">Remove</a>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>

  <div class="card p-4">
    <h5 class="listing-title mb-3">Recent Activity</h5>
    <p class="text-muted small mb-3">Messages exchanged: <?= $message_count ?></p>

    <?php if (count($recent_activity) === 0): ?>
    <p class="text-muted mb-0">No recent activity recorded.</p>
    <?php else: ?>
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead>
          <tr>
            <th>Type</th>
            <th>Item</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent_activity as $activity): ?>
          <tr>
            <td><?= ucfirst(htmlspecialchars($activity['activity_type'])) ?></td>
            <td>
              <?php if (!empty($activity['product_id'])): ?>
              <a href="listing.php?id=<?= (int) $activity['product_id'] ?>">
                <?= htmlspecialchars($activity['title']) ?>
              </a>
              <?php else: ?>
              <?= htmlspecialchars($activity['title']) ?>
              <?php endif; ?>
            </td>
            <td><?= date('d M Y', strtotime($activity['activity_date'])) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

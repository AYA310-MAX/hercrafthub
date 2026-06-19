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
    'SELECT full_name, email, role, is_active, is_verified, created_at, profile_image FROM users WHERE id = ? LIMIT 1',
    'i',
    [$user_id]
);

if ($account === null) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$nav_avatar_src = !empty($account['profile_image']) ? 'uploads/avatars/' . htmlspecialchars($account['profile_image']) : '';

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
  <link rel="stylesheet" href="css/dashboard.css?v=<?= filemtime('css/dashboard.css') ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<div class="dashboard-layout">
  <!-- ── Left Sidebar ── -->
  <aside class="dashboard-sidebar">
    <div class="sidebar-brand-wrapper">
      <a href="index.php" class="brand">✦ HerCraft<span>Hub</span></a>
    </div>

    <div class="sidebar-user">
      <div class="user-avatar<?= $nav_avatar_src !== '' ? ' user-avatar-img' : '' ?>">
        <?php if ($nav_avatar_src !== ''): ?>
          <img src="<?= htmlspecialchars($nav_avatar_src) ?>" alt="<?= htmlspecialchars($account['full_name']) ?>">
        <?php else: ?>
          <?= strtoupper(substr($account['full_name'], 0, 1)) ?>
        <?php endif; ?>
      </div>
      <div class="sidebar-user-info">
        <span class="sidebar-user-name" title="<?= htmlspecialchars($account['full_name']) ?>">
          <?= htmlspecialchars($account['full_name']) ?>
        </span>
        <span class="sidebar-user-role status-badge active">
          <?= ucfirst(htmlspecialchars($account['role'])) ?>
        </span>
      </div>
    </div>

    <ul class="sidebar-menu">
      <li class="sidebar-section-title">Main Menu</li>
      <li>
        <a href="dashboard.php" class="active">
          <i class="ti ti-layout-dashboard"></i> Dashboard
        </a>
      </li>
      <li>
        <a href="browse.php">
          <i class="ti ti-search"></i> Browse Products
        </a>
      </li>
      <?php if ($role === 'seller'): ?>
      <li>
        <a href="sell.php">
          <i class="ti ti-plus"></i> Post An Item
        </a>
      </li>
      <?php endif; ?>

      <li class="sidebar-section-title mt-4">Account</li>
      <li>
        <a href="profile.php">
          <i class="ti ti-user"></i> Edit Profile
        </a>
      </li>
      <li>
        <a href="index.php">
          <i class="ti ti-home"></i> View Shop
        </a>
      </li>
      <li>
        <a href="#" class="logout-trigger" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="ti ti-logout text-danger"></i> Logout
        </a>
      </li>
    </ul>

    <!-- Theme toggler in sidebar footer -->
    <div class="theme-toggle-wrapper">
      <span class="text-muted small">Theme</span>
      <div class="d-flex align-items-center gap-2">
        <i class="ti ti-sun text-muted" style="font-size:0.9rem;"></i>
        <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode"></button>
        <i class="ti ti-moon text-muted" style="font-size:0.9rem;"></i>
      </div>
    </div>
  </aside>

  <!-- ── Right Main Content Area ── -->
  <div class="dashboard-main">
    <!-- Header ── -->
    <header class="dashboard-header">
      <div class="dashboard-welcome">
        <h4>Welcome back, <?= htmlspecialchars(explode(' ', $account['full_name'])[0]) ?>!</h4>
        <p>Manage your <?= htmlspecialchars($account['role']) ?> panel and view analytics.</p>
      </div>

      <div class="dashboard-header-right">
        <?php if ($role === 'buyer'): ?>
          <a href="#wishlist-section" class="header-icon-btn" title="My Wishlist">
            <i class="ti ti-heart"></i>
            <?php if ($wishlist_count > 0): ?>
              <span class="badge-dot"></span>
            <?php endif; ?>
          </a>
        <?php endif; ?>
        <div class="user-avatar<?= $nav_avatar_src !== '' ? ' user-avatar-img' : '' ?>">
          <?php if ($nav_avatar_src !== ''): ?>
            <img src="<?= htmlspecialchars($nav_avatar_src) ?>" alt="<?= htmlspecialchars($account['full_name']) ?>">
          <?php else: ?>
            <?= strtoupper(substr($account['full_name'], 0, 1)) ?>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <!-- Main Content ── -->
    <main class="dashboard-content">
      <?= render_flash_messages() ?>

      <div class="alert alert-secondary small py-2 mb-4 border-0" style="background-color: var(--cream);">
        <i class="ti ti-clock me-1 text-muted"></i> Dashboard sessions expire after 30 minutes of inactivity for your security.
      </div>

      <!-- Stats Cards ── -->
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-icon-wrapper">
            <i class="ti ti-shield"></i>
          </div>
          <div class="stat-info">
            <span class="stat-value"><?= ucfirst(htmlspecialchars($account['role'])) ?></span>
            <span class="stat-title">Role</span>
          </div>
        </div>

        <div class="stat-card">
          <div class="stat-icon-wrapper">
            <i class="ti ti-circle-check"></i>
          </div>
          <div class="stat-info">
            <span class="stat-value"><?= (int)$account['is_active'] ? 'Active' : 'Suspended' ?></span>
            <span class="stat-title">Status</span>
          </div>
        </div>

        <?php if ($role === 'seller'): ?>
          <div class="stat-card">
            <div class="stat-icon-wrapper">
              <i class="ti ti-package"></i>
            </div>
            <div class="stat-info">
              <span class="stat-value"><?= $active_listings ?></span>
              <span class="stat-title">Active Items</span>
            </div>
          </div>

          <div class="stat-card">
            <div class="stat-icon-wrapper">
              <i class="ti ti-cash"></i>
            </div>
            <div class="stat-info">
              <span class="stat-value"><?= format_price($total_earnings) ?></span>
              <span class="stat-title">Total Earnings</span>
            </div>
          </div>
        <?php else: ?>
          <div class="stat-card">
            <div class="stat-icon-wrapper">
              <i class="ti ti-heart"></i>
            </div>
            <div class="stat-info">
              <span class="stat-value"><?= $wishlist_count ?></span>
              <span class="stat-title">Saved Items</span>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <!-- Seller Tables ── -->
      <?php if ($role === 'seller'): ?>
        <div class="dashboard-card">
          <div class="dashboard-card-title">
            <span>My Listed Products</span>
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
                      <td class="fw-bold text-dark"><?= format_price($product['price']) ?></td>
                      <td><?= (int)$product['quantity'] ?></td>
                      <td>
                        <span class="status-badge <?= (int)$product['is_active'] ? 'active' : 'inactive' ?>">
                          <?= (int)$product['is_active'] ? 'Visible' : 'Hidden' ?>
                        </span>
                      </td>
                      <td>
                        <?php if ((int)$product['is_sold'] || (int)$product['quantity'] < 1): ?>
                          <span class="status-badge inactive">Sold Out</span>
                        <?php else: ?>
                          <span class="status-badge active">Available</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="d-flex gap-1 flex-wrap">
                          <a href="edit_listing.php?id=<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                          <a href="php/listing_manage.php?action=toggle&id=<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-secondary">
                            <?= (int)$product['is_active'] ? 'Hide' : 'Show' ?>
                          </a>
                          <a href="php/listing_manage.php?action=delete&id=<?= (int)$product['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this listing permanently?')">Delete</a>
                        </div>
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
          <div class="dashboard-card">
            <h5 class="dashboard-card-title">Recent Sales Tracking</h5>
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
                        <a href="listing.php?id=<?= (int)$sale['product_id'] ?>" class="fw-bold text-decoration-none" style="color: var(--purple-mid);">
                          <?= htmlspecialchars($sale['title']) ?>
                        </a>
                      </td>
                      <td><?= htmlspecialchars($sale['buyer_name']) ?></td>
                      <td class="fw-bold text-success"><?= format_price($sale['item_total']) ?></td>
                      <td><?= date('d M Y', strtotime($sale['created_at'])) ?></td>
                      <td>
                        <form action="php/update_tracking_action.php" method="POST" class="d-flex align-items-center gap-2">
                          <input type="hidden" name="sale_id" value="<?= (int)$sale['sale_id'] ?>">
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

      <!-- Buyer Tables ── -->
      <?php if ($role === 'buyer' && count($purchase_history) > 0): ?>
        <div class="dashboard-card">
          <h5 class="dashboard-card-title">My Purchases</h5>
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
                    <td><strong><?= htmlspecialchars($order['title']) ?></strong></td>
                    <td><?= htmlspecialchars($order['seller_name']) ?></td>
                    <td class="small"><?= htmlspecialchars($order['delivery_address']) ?></td>
                    <td class="fw-bold text-success"><?= format_price($order['total_amount']) ?></td>
                    <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                    <td>
                      <a href="track_order.php?sale_id=<?= (int)$order['sale_id'] ?>" class="btn btn-sm btn-outline-primary">Track Order</a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>

      <!-- Wishlist Section ── -->
      <?php if ($role === 'buyer' || ($role === 'seller' && count($wishlist_items) > 0)): ?>
        <div class="dashboard-card" id="wishlist-section">
          <h5 class="dashboard-card-title">My Wishlist</h5>
          <?php if (count($wishlist_items) === 0): ?>
            <p class="text-muted mb-0">You have not saved any items yet.</p>
          <?php else: ?>
            <div class="row g-3">
              <?php foreach ($wishlist_items as $item): ?>
                <div class="col-md-6">
                  <div class="card p-3 h-100 border-0" style="background-color: var(--cream); border-radius: 10px;">
                    <div class="d-flex gap-3">
                      <img src="<?= htmlspecialchars(product_image_src($item['image'])) ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="dashboard-thumb">
                      <div class="flex-grow-1">
                        <span class="badge-category"><?= htmlspecialchars($item['category_name']) ?></span>
                        <h6 class="mt-1 listing-title text-dark"><?= htmlspecialchars($item['title']) ?></h6>
                        <p class="listing-price mb-2 text-primary"><?= format_price($item['price']) ?></p>
                        <div class="d-flex gap-2">
                          <a href="listing.php?id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-primary">View</a>
                          <a href="php/wishlist_action.php?action=remove&product_id=<?= (int)$item['id'] ?>&redirect=dashboard" class="btn btn-sm btn-outline-danger">Remove</a>
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

      <!-- Recent Activity Section ── -->
      <div class="dashboard-card">
        <h5 class="dashboard-card-title">Recent Activity</h5>
        <p class="text-muted small mb-3">Messages exchanged: <?= $message_count ?></p>

        <?php if (count($recent_activity) === 0): ?>
          <p class="text-muted mb-0">No recent activity recorded.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle">
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
                    <td>
                      <span class="status-badge <?= $activity['activity_type'] === 'message' ? 'active' : 'inactive' ?>" style="text-transform: capitalize;">
                        <?= htmlspecialchars($activity['activity_type']) ?>
                      </span>
                    </td>
                    <td>
                      <?php if (!empty($activity['product_id'])): ?>
                        <a href="listing.php?id=<?= (int)$activity['product_id'] ?>" class="fw-bold text-decoration-none text-dark">
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
    </main>
  </div>
</div>

<?php include 'includes/logout_modal.php'; echo render_goodbye_modal(); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    $_SESSION['error'] = 'The requested listing was not found.';
    header('Location: browse.php');
    exit;
}

$product = db_fetch_one(
    $conn,
    'SELECT p.*, c.name AS category_name, u.full_name AS seller_name,
            u.profile_image AS seller_avatar, u.is_verified,
            u.id AS seller_user_id, YEAR(u.created_at) AS seller_since
     FROM products p
     INNER JOIN categories c ON p.category_id = c.id
     INNER JOIN users u ON p.seller_id = u.id
     WHERE p.id = ? AND p.is_active = 1 AND p.is_sold = 0 AND p.quantity > 0
     LIMIT 1',
    'i',
    [$id]
);

if ($product === null) {
    $_SESSION['error'] = 'The requested listing was not found or is no longer available.';
    header('Location: browse.php');
    exit;
}

$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $wishlist_row = db_fetch_one(
        $conn,
        'SELECT id FROM wishlists WHERE user_id = ? AND product_id = ? LIMIT 1',
        'ii',
        [(int) $_SESSION['user_id'], $id]
    );
    $in_wishlist = $wishlist_row !== null;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($product['title']) ?> – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
  <a href="browse.php" class="back-btn">
    <i class="ti ti-arrow-left"></i> Back
  </a>
</div>

<div class="container my-5">
  <?= render_flash_messages() ?>

  <div class="row g-5">

    <div class="col-md-6">
      <img src="<?= htmlspecialchars(product_image_src($product['image'])) ?>"
           alt="<?= htmlspecialchars($product['title']) ?>"
           class="listing-detail-img">
    </div>

    <div class="col-md-6">
      <span class="badge-category"><?= htmlspecialchars($product['category_name']) ?></span>
      <h2 class="mt-2 listing-title"><?= htmlspecialchars($product['title']) ?></h2>

      <h3 class="listing-price my-3"><?= format_price($product['price']) ?></h3>

      <?php if ((int) $product['quantity'] > 1): ?>
      <p class="mb-2"><span class="status-badge active"><?= (int) $product['quantity'] ?> in stock</span></p>
      <?php endif; ?>

      <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>

      <div class="row g-2 my-3">
        <div class="col-4">
          <div class="card p-3 text-center">
            <small class="text-muted">Condition</small>
            <strong><?= htmlspecialchars($product['condition_type']) ?></strong>
          </div>
        </div>
        <div class="col-4">
          <div class="card p-3 text-center">
            <small class="text-muted">Location</small>
            <strong><?= htmlspecialchars($product['location'] ?: 'South Africa') ?></strong>
          </div>
        </div>
        <div class="col-4">
          <div class="card p-3 text-center">
            <small class="text-muted">Available</small>
            <strong><?= (int) $product['quantity'] ?></strong>
          </div>
        </div>
      </div>

      <div class="card p-3 mb-4 seller-card">
        <div class="d-flex align-items-center gap-3">
          <?= render_user_avatar($product['seller_name'], $product['seller_avatar'] ?? null, 'user-avatar-md') ?>
          <div>
            <strong><?= htmlspecialchars($product['seller_name']) ?></strong>
            <br><small class="text-muted">
              <?php if ((int) $product['is_verified']): ?>Verified Seller<?php else: ?>Seller<?php endif; ?>
              since <?= htmlspecialchars((string) $product['seller_since']) ?>
            </small>
          </div>
        </div>
      </div>

      <div class="d-grid gap-2">
        <?php if (isset($_SESSION['user_id'])): ?>
          <?php if ((int) $_SESSION['user_id'] !== (int) $product['seller_user_id']): ?>
            <?php if ($_SESSION['role'] !== 'seller'): ?>
            <a href="<?= htmlspecialchars(url_for('checkout.php?product_id=' . (int) $product['id'])) ?>"
               class="btn btn-primary py-2 w-100">
              <i class="ti ti-shopping-cart me-2"></i>Proceed to Checkout
            </a>
            <a href="message.php?product_id=<?= (int) $product['id'] ?>" class="btn btn-outline-primary py-2">
              <i class="ti ti-message me-2"></i>Message Seller
            </a>
            <button class="btn btn-outline-primary py-2 wishlist-btn<?= $in_wishlist ? ' saved' : '' ?>"
                    id="wishlistBtn"
                    data-product-id="<?= (int) $product['id'] ?>"
                    data-in-wishlist="<?= $in_wishlist ? '1' : '0' ?>">
              <i class="ti ti-<?= $in_wishlist ? 'heart-filled' : 'heart' ?> me-2"></i>
              <span class="wishlist-label"><?= $in_wishlist ? 'Saved to Wishlist' : 'Save to Wishlist' ?></span>
            </button>
            <?php endif; ?>
          <?php else: ?>
          <a href="edit_listing.php?id=<?= (int) $product['id'] ?>" class="btn btn-outline-primary py-2">
            <i class="ti ti-edit me-2"></i>Edit Listing
          </a>
          <?php endif; ?>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary py-2">Login to Contact Seller</a>
          <a href="register.php" class="btn btn-outline-primary py-2">Join Free to Buy</a>
        <?php endif; ?>
        
        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller'): ?>
        <a href="browse.php" class="btn btn-outline-secondary py-2">
          <i class="ti ti-arrow-left me-2"></i>Back to Browse
        </a>
        <?php else: ?>
        <a href="dashboard.php" class="btn btn-outline-secondary py-2">
          <i class="ti ti-arrow-left me-2"></i>Back to Dashboard
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

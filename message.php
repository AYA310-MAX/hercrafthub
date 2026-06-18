<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';
require_once 'includes/auth.php';

require_login();

$product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

if ($product_id <= 0) {
    $_SESSION['error'] = 'Invalid product selected.';
    header('Location: dashboard.php');
    exit;
}

$product = db_fetch_one(
    $conn,
    'SELECT p.id, p.title, p.seller_id, u.full_name AS seller_name
     FROM products p
     INNER JOIN users u ON p.seller_id = u.id
     WHERE p.id = ? AND p.is_active = 1 AND p.is_sold = 0
     LIMIT 1',
    'i',
    [$product_id]
);

if ($product === null) {
    $_SESSION['error'] = 'The selected listing is not available.';
    header('Location: browse.php');
    exit;
}

if ((int) $_SESSION['user_id'] === (int) $product['seller_id']) {
    $_SESSION['error'] = 'You cannot message yourself about your own listing.';
    header('Location: listing.php?id=' . $product_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Message Seller – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container my-5" style="max-width:640px;">
  <div class="card p-4 p-md-5">
    <h2 class="listing-title mb-2">Message Seller</h2>
    <p class="text-muted mb-4">
      Regarding: <strong><?= htmlspecialchars($product['title']) ?></strong><br>
      Seller: <?= htmlspecialchars($product['seller_name']) ?>
    </p>

    <?= render_flash_messages() ?>

    <form method="POST" action="php/message_action.php">
      <input type="hidden" name="product_id" value="<?= (int) $product_id ?>">
      <input type="hidden" name="receiver_id" value="<?= (int) $product['seller_id'] ?>">

      <div class="mb-4">
        <label class="form-label">Your Message</label>
        <textarea name="body" class="form-control" rows="5" maxlength="1000" required
                  placeholder="Write your enquiry about this item."></textarea>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2">Send Message</button>
      <a href="listing.php?id=<?= (int) $product_id ?>" class="btn btn-outline-secondary w-100 mt-2">
        Cancel
      </a>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

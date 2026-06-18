<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';
require_once 'includes/auth.php';

require_login();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = (int) $_SESSION['user_id'];

if ($id <= 0) {
    $_SESSION['error'] = 'Invalid listing selected.';
    header('Location: dashboard.php');
    exit;
}

$product = db_fetch_one(
    $conn,
    'SELECT p.*, c.name AS category_name
     FROM products p
     INNER JOIN categories c ON p.category_id = c.id
     WHERE p.id = ? AND p.seller_id = ?
     LIMIT 1',
    'ii',
    [$id, $user_id]
);

if ($product === null) {
    $_SESSION['error'] = 'You do not have permission to edit this listing.';
    header('Location: dashboard.php');
    exit;
}

$categories = get_categories($conn);
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Listing – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
  <a href="dashboard.php" class="back-btn">
    <i class="ti ti-arrow-left"></i> Back to Dashboard
  </a>
</div>

<div class="container my-5" style="max-width:700px;">
  <div class="card p-4 p-md-5">
    <h2 class="listing-title mb-4">Edit Listing</h2>
    <?= render_flash_messages() ?>

    <form action="php/update_listing_action.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">

      <div class="mb-3">
        <label class="form-label">Item Title</label>
        <input type="text" name="title" class="form-control" maxlength="100" required
               value="<?= htmlspecialchars($product['title']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-select" required>
          <?php foreach ($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat['name']) ?>"
            <?= $cat['name'] === $product['category_name'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4" maxlength="500" required><?= htmlspecialchars($product['description']) ?></textarea>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-sm-4">
          <label class="form-label">Price (ZAR)</label>
          <div class="input-group">
            <span class="input-group-text input-group-currency">R</span>
            <input type="number" name="price" class="form-control"
                   min="1" step="0.01" required value="<?= htmlspecialchars((string) $product['price']) ?>">
          </div>
        </div>
        <div class="col-sm-4">
          <label class="form-label">Quantity</label>
          <input type="number" name="quantity" class="form-control"
                 min="0" max="999" required value="<?= (int) ($product['quantity'] ?? 1) ?>">
          <small class="text-muted">Set to 0 to mark as sold out</small>
        </div>
        <div class="col-sm-4">
          <label class="form-label">Condition</label>
          <select name="condition" class="form-select" required>
            <?php foreach (['New', 'Like New', 'Good', 'Fair'] as $cond): ?>
            <option value="<?= $cond ?>" <?= $product['condition_type'] === $cond ? 'selected' : '' ?>>
              <?= $cond ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control"
               value="<?= htmlspecialchars((string) $product['location']) ?>">
      </div>

      <div class="mb-3">
        <label class="form-label">Current Image</label>
        <div>
          <img src="<?= htmlspecialchars(product_image_src($product['image'])) ?>"
               alt="Current product image" class="sell-preview-img">
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label">Replace Image (optional)</label>
        <input type="file" name="image" class="form-control" accept="image/*">
        <small class="text-muted">JPG, PNG or GIF. Maximum 2MB.</small>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2">Save Changes</button>
    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

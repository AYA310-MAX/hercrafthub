<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>View Listing – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
  <a href="javascript:history.back()" class="back-btn">
    <i class="ti ti-arrow-left"></i> Back
  </a>
</div>

<div class="container my-5">

  <?php
    // Placeholder data — replaced with real DB query in Phase 3
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
    $product = [
      'title'       => 'Custom LED Phone Case',
      'category'    => 'Tech Crafts',
      'price'       => 180,
      'condition'   => 'New',
      'location'    => 'Johannesburg, Gauteng',
      'description' => 'A beautiful handcrafted LED phone case made with love in South Africa. 
                        Perfect for iPhone and Samsung models. Customisable colours available 
                        on request. Ships nationwide within 3–5 working days.',
      'seller'      => 'Ayasha M.',
      'seller_since'=> '2024',
      'image'       => 'https://via.placeholder.com/600x400/9B59B6/white?text=Product+Image',
    ];
  ?>

  <div class="row g-5">

    <!-- ── Product Image ── -->
    <div class="col-md-6">
      <img src="<?= $product['image'] ?>" alt="<?= $product['title'] ?>"
           style="width:100%;border-radius:var(--radius);box-shadow:var(--shadow);">
    </div>

    <!-- ── Product Details ── -->
    <div class="col-md-6">
      <span class="badge-category"><?= $product['category'] ?></span>
      <h2 class="mt-2 fw-bold" style="color:var(--text-dark);"><?= $product['title'] ?></h2>

      <h3 style="color:var(--purple);font-weight:700;" class="my-3">
        R<?= number_format($product['price'],2) ?>
      </h3>

      <p class="text-muted"><?= $product['description'] ?></p>

      <div class="row g-2 my-3">
        <div class="col-6">
          <div class="card p-3 text-center">
            <small class="text-muted">Condition</small>
            <strong><?= $product['condition'] ?></strong>
          </div>
        </div>
        <div class="col-6">
          <div class="card p-3 text-center">
            <small class="text-muted">Location</small>
            <strong><?= $product['location'] ?></strong>
          </div>
        </div>
      </div>

      <!-- Seller info -->
      <div class="card p-3 mb-4" style="border-left:4px solid var(--purple);">
        <div class="d-flex align-items-center gap-3">
          <div style="width:48px;height:48px;border-radius:50%;
                      background:linear-gradient(135deg,var(--purple),var(--pink));
                      display:flex;align-items:center;justify-content:center;
                      color:white;font-weight:700;font-size:1.2rem;">
            <?= strtoupper(substr($product['seller'],0,1)) ?>
          </div>
          <div>
            <strong><?= $product['seller'] ?></strong>
            <br><small class="text-muted">Verified Seller since <?= $product['seller_since'] ?> ✓</small>
          </div>
        </div>
      </div>

      <!-- Action buttons -->
      <div class="d-grid gap-2">
        <?php if(isset($_SESSION['user_id'])): ?>
          <button class="btn btn-primary py-2">💬 Message Seller</button>
          <button class="btn btn-outline-primary py-2" id="wishlistBtn">♡ Save to Wishlist</button>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary py-2">Login to Contact Seller</a>
          <a href="register.php" class="btn btn-outline-primary py-2">Join Free to Buy</a>
        <?php endif; ?>
        <a href="browse.php" class="btn btn-outline-secondary py-2">← Back to Browse</a>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
// Wishlist toggle
document.getElementById('wishlistBtn')?.addEventListener('click', function() {
  const saved = this.textContent.includes('♡');
  this.textContent = saved ? '♥ Saved to Wishlist' : '♡ Save to Wishlist';
  this.style.background = saved ? 'var(--pink-light)' : '';
});
</script>
</body>
</html>
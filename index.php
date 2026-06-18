<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';

$categories = get_categories($conn);
$featured   = db_fetch_all(
    $conn,
    'SELECT p.id, p.title, p.price, p.image, c.name AS category_name
     FROM products p
     INNER JOIN categories c ON p.category_id = c.id
     WHERE p.is_active = 1 AND p.is_sold = 0 AND p.quantity > 0
     ORDER BY p.created_at DESC
     LIMIT 4'
);
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HerCraft Hub – Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="page-header" style="padding:80px 0;">
  <div class="container text-center">
    <h1 style="font-size:2.8rem;">Where Women Create,<br>Connect and Sell</h1>
    <p class="mt-3" style="font-size:1.1rem;opacity:0.85;max-width:560px;margin:0 auto;">
      South Africa's first C2C marketplace built for women selling handmade goods, tech crafts and digital products.
    </p>
    <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
      <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller'): ?>
      <a href="browse.php" class="btn btn-outline-light px-4 py-2"
         style="border-radius:4px;letter-spacing:0.08em;font-size:0.85rem;text-transform:uppercase; color:#fff !important; border-color:#fff !important;">
        Browse Listings
      </a>
      <?php endif; ?>
      <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'buyer'): ?>
      <a href="<?= isset($_SESSION['user_id']) ? 'sell.php' : 'register.php' ?>" class="btn btn-light px-4 py-2"
         style="border-radius:4px;letter-spacing:0.08em;font-size:0.85rem;
                text-transform:uppercase;color:#4a154b !important;font-weight:600;">
        <?= isset($_SESSION['user_id']) && $_SESSION['role'] === 'seller' ? 'List an Item' : 'Start Selling' ?>
      </a>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller'): ?>
<section class="container my-5">
  <h2 class="section-title text-center mb-2">Shop by <span>Category</span></h2>
  <div class="section-divider"></div>
  <div class="d-flex flex-wrap justify-content-center gap-3">
    <?php foreach ($categories as $cat): ?>
    <a href="browse.php?cat=<?= urlencode($cat['name']) ?>"
       class="badge-category text-decoration-none">
      <?= htmlspecialchars($cat['name']) ?>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller'): ?>
<section class="container mb-5">
  <h2 class="section-title mb-2">Featured <span>Listings</span></h2>
  <div class="section-divider" style="margin:12px 0 24px;"></div>
  <div class="row g-4">
    <?php if (count($featured) === 0): ?>
    <div class="col-12 text-center py-4">
      <p class="text-muted">No listings yet. Be the first to sell on HerCraft Hub.</p>
      <a href="sell.php" class="btn btn-outline-primary">List an Item</a>
    </div>
    <?php else: ?>
    <?php foreach ($featured as $item): ?>
    <div class="col-sm-6 col-lg-3">
      <div class="card listing-card h-100">
        <img src="<?= htmlspecialchars(product_image_src($item['image'])) ?>"
             class="card-img-top" alt="<?= htmlspecialchars($item['title']) ?>">
        <div class="card-body">
          <span class="badge-category"><?= htmlspecialchars($item['category_name']) ?></span>
          <h6 class="mt-2 listing-title"><?= htmlspecialchars($item['title']) ?></h6>
          <p class="small mt-1 text-muted">Handcrafted in South Africa</p>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <span class="listing-price"><?= format_price($item['price']) ?></span>
            <a href="listing.php?id=<?= (int) $item['id'] ?>" class="btn btn-primary btn-sm">View</a>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <div class="text-center mt-4">
    <a href="browse.php" class="btn btn-outline-primary">View All Listings</a>
  </div>
</section>
<?php endif; ?>

<section style="background:var(--cream-dark);padding:60px 0;">
  <div class="container">
    <h2 class="section-title text-center mb-2">Why <span>HerCraft Hub?</span></h2>
    <div class="section-divider"></div>
    <div class="row g-4 text-center">
      <?php
        $features = [
          ['icon' => 'shield',        'title' => 'Verified Sellers', 'desc' => 'Every seller is verified for your safety and peace of mind.'],
          ['icon' => 'lock',          'title' => 'Secure Payments',  'desc' => 'South African payment gateways. Pay safely, every time.'],
          ['icon' => 'device-mobile', 'title' => 'Mobile Friendly',  'desc' => 'Built for South Africa. Works great on any phone.'],
          ['icon' => 'rocket',        'title' => 'Easy to Sell',     'desc' => 'List your item in under 2 minutes. No experience needed.'],
        ];
        foreach ($features as $f):
      ?>
      <div class="col-sm-6 col-lg-3">
        <div class="card feature-card p-4 h-100 text-center">
          <div class="feature-icon mx-auto">
            <i class="ti ti-<?= $f['icon'] ?>"></i>
          </div>
          <h6 class="mt-3 feature-title"><?= $f['title'] ?></h6>
          <p class="feature-desc mt-2"><?= $f['desc'] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';

$categories   = get_categories($conn);
$max_price    = get_max_product_price($conn);
$listings     = db_fetch_all(
    $conn,
    'SELECT p.id, p.title, p.price, p.image, c.name AS category_name, u.full_name AS seller_name
     FROM products p
     INNER JOIN categories c ON p.category_id = c.id
     INNER JOIN users u ON p.seller_id = u.id
     WHERE p.is_active = 1 AND p.is_sold = 0 AND p.quantity > 0
     ORDER BY p.created_at DESC'
);
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Browse Listings – HerCraft Hub</title>
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

<section class="page-header">
  <div class="container">
    <h1>Browse Listings</h1>
    <p>Discover handmade goods, tech crafts and digital products</p>
  </div>
</section>

<div class="container my-5">
  <?= render_flash_messages() ?>

  <div class="row g-4">

    <div class="col-lg-3">
      <div class="card p-4">
        <h6 class="filter-panel-title mb-3">Filter and Search</h6>

        <input type="text" id="searchInput" class="form-control mb-3"
               placeholder="Search listings...">

        <label class="form-label">Category</label>
        <select id="categoryFilter" class="form-select mb-3">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
          <option value="<?= htmlspecialchars($cat['name']) ?>">
            <?= htmlspecialchars($cat['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>

        <label class="form-label">Max Price: <span id="priceLabel">R<?= $max_price ?></span></label>
        <input type="range" id="priceRange" class="form-range mb-3"
               min="0" max="<?= $max_price ?>" value="<?= $max_price ?>" step="50">

        <label class="form-label">Sort By</label>
        <select id="sortFilter" class="form-select mb-3">
          <option value="newest">Newest First</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
        </select>

        <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
        <button class="btn btn-outline-primary w-100 mt-2" id="clearFilters">Clear</button>
      </div>
    </div>

    <div class="col-lg-9">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted small" id="resultsCount">
          Showing <?= count($listings) ?> listing<?= count($listings) !== 1 ? 's' : '' ?>
        </span>
      </div>

      <div class="row g-4" id="listingsGrid">
        <?php if (count($listings) === 0): ?>
        <div class="col-12 text-center py-5">
          <i class="ti ti-search" style="font-size:3rem;color:var(--text-muted);"></i>
          <h5 class="mt-3 listing-title">No listings available</h5>
          <p class="text-muted">Check back soon or list the first item.</p>
          <a href="sell.php" class="btn btn-outline-primary">Sell an Item</a>
        </div>
        <?php else: ?>
        <?php foreach ($listings as $item): ?>
        <div class="col-sm-6 col-xl-4 listing-item"
             data-category="<?= htmlspecialchars($item['category_name']) ?>"
             data-price="<?= (int) $item['price'] ?>"
             data-name="<?= htmlspecialchars(strtolower($item['title'])) ?>">
          <div class="card listing-card h-100">
            <img src="<?= htmlspecialchars(product_image_src($item['image'])) ?>"
                 class="card-img-top" alt="<?= htmlspecialchars($item['title']) ?>">
            <div class="card-body">
              <span class="badge-category"><?= htmlspecialchars($item['category_name']) ?></span>
              <h6 class="mt-2 listing-title"><?= htmlspecialchars($item['title']) ?></h6>
              <p class="small mt-1 text-muted"><?= htmlspecialchars($item['seller_name']) ?></p>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="listing-price"><?= format_price($item['price']) ?></span>
                <a href="listing.php?id=<?= (int) $item['id'] ?>" class="btn btn-primary btn-sm">
                  View Item
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <div id="noResults" class="text-center py-5 d-none">
        <i class="ti ti-search" style="font-size:3rem;color:var(--text-muted);"></i>
        <h5 class="mt-3 listing-title">No listings found</h5>
        <p class="text-muted">Try adjusting your filters</p>
        <button class="btn btn-outline-primary" id="clearFilters2">Clear Filters</button>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>window.HCH_MAX_PRICE = <?= (int) $max_price ?>;</script>
<script src="js/main.js"></script>
<script src="js/browse.js"></script>
</body>
</html>

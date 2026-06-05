<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
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

<!-- ── Page Header ── -->
<section class="page-header">

<!-- ── Page Header ── -->
<section style="background:linear-gradient(135deg,#6B2D8B,#E91E8C);padding:40px 0;color:white;">
  <div class="container">
    <h1 style="font-weight:700;">Browse Listings ✦</h1>
    <p style="opacity:0.9;">Discover handmade goods, tech crafts &amp; digital products</p>
  </div>
</section>

<div class="container my-5">
  <div class="row g-4">

    <!-- ── Sidebar Filters ── -->
    <div class="col-lg-3">
      <div class="card p-4">
        <h6 class="fw-bold mb-3" style="color:var(--purple);">🔍 Filter &amp; Search</h6>

        <!-- Search box -->
        <input type="text" id="searchInput" class="form-control mb-3" 
               placeholder="Search listings...">

        <!-- Category filter -->
        <label class="form-label fw-500">Category</label>
        <select id="categoryFilter" class="form-select mb-3">
          <option value="">All Categories</option>
          <option value="Tech Crafts">💻 Tech Crafts</option>
          <option value="Handmade">🧵 Handmade</option>
          <option value="Digital Art">🎨 Digital Art</option>
          <option value="Accessories">📱 Accessories</option>
          <option value="Bundles">📦 Bundles</option>
          <option value="Beauty Tech">✨ Beauty Tech</option>
        </select>

        <!-- Price range -->
        <label class="form-label fw-500">Max Price: <span id="priceLabel">R500</span></label>
        <input type="range" id="priceRange" class="form-range mb-3"
               min="0" max="2000" value="500" step="50">

        <!-- Sort -->
        <label class="form-label fw-500">Sort By</label>
        <select id="sortFilter" class="form-select mb-3">
          <option value="newest">Newest First</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
        </select>

        <button class="btn btn-primary w-100" id="applyFilters">Apply Filters</button>
        <button class="btn btn-outline-primary w-100 mt-2" id="clearFilters">Clear</button>
      </div>
    </div>

    <!-- ── Listings Grid ── -->
    <div class="col-lg-9">

      <!-- Results count -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted small" id="resultsCount">Showing all listings</span>
      </div>

      <div class="row g-4" id="listingsGrid">
        <?php
          // Placeholder listings — replaced with real DB data in Phase 3
          $items = [
            [1,"Custom LED Phone Case","Tech Crafts",  180,"purple"],
            [2,"Crochet Laptop Sleeve","Handmade",     250,"pink"],
            [3,"Digital Planner PDF",  "Digital Art",   80,"teal"],
            [4,"Beaded Earrings Set",  "Accessories",  120,"coral"],
            [5,"Self-Care Tech Bundle","Bundles",      350,"amber"],
            [6,"Glow Skin Device",     "Beauty Tech",  420,"green"],
            [7,"PCB Art Wall Print",   "Digital Art",  150,"blue"],
            [8,"Knitted Cable Cover",  "Handmade",      95,"pink"],
          ];
          foreach($items as $item):
        ?>
        <div class="col-sm-6 col-xl-4 listing-item"
             data-category="<?= $item[2] ?>"
             data-price="<?= $item[3] ?>"
             data-name="<?= strtolower($item[1]) ?>">
          <div class="card h-100">
            <img src="https://via.placeholder.com/400x200/9B59B6/white?text=<?= urlencode($item[1]) ?>"
                 class="card-img-top" alt="<?= $item[1] ?>">
            <div class="card-body">
              <span class="badge-category"><?= $item[2] ?></span>
              <h6 class="mt-2 fw-bold"><?= $item[1] ?></h6>
              <p class="text-muted small">Sold by a verified seller 🇿🇦</p>
              <div class="d-flex justify-content-between align-items-center mt-3">
                <span style="color:var(--purple);font-weight:700;">
                  R<?= number_format($item[3],2) ?>
                </span>
                <a href="listing.php?id=<?= $item[0] ?>" class="btn btn-primary btn-sm">
                  View Item
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- No results message -->
      <div id="noResults" class="text-center py-5 d-none">
        <p style="font-size:3rem;">🔍</p>
        <h5 style="color:var(--purple);">No listings found</h5>
        <p class="text-muted">Try adjusting your filters</p>
        <button class="btn btn-outline-primary" id="clearFilters2">Clear Filters</button>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script src="js/browse.js"></script>
</body>
</html>
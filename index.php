<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HerCraft Hub – Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- ── Hero Section ── -->
<section style="background:linear-gradient(135deg,#6B2D8B,#E91E8C);padding:80px 0;color:white;">
  <div class="container text-center">
    <h1 style="font-size:2.8rem;font-weight:700;">Where Women Create,<br>Connect &amp; Sell ✦</h1>
    <p class="mt-3" style="font-size:1.1rem;opacity:0.9;max-width:560px;margin:0 auto;">
      South Africa's first C2C marketplace built for women selling handmade goods, tech crafts &amp; digital products.
    </p>
    <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
      <a href="browse.php" class="btn btn-light px-4 py-2 rounded-pill" style="color:var(--purple);font-weight:600;">Browse Listings</a>
      <a href="register.php" class="btn btn-outline-light px-4 py-2 rounded-pill">Start Selling</a>
    </div>
  </div>
</section>

<!-- ── Category Pills ── -->
<section class="container my-5">
  <h2 class="section-title text-center mb-4">Shop by <span>Category</span></h2>
  <div class="d-flex flex-wrap justify-content-center gap-3">
    <?php
      $cats = ["💻 Tech Crafts","🧵 Handmade","🎨 Digital Art","📱 Accessories","📦 Bundles","✨ Beauty Tech"];
      foreach($cats as $c):
    ?>
      <a href="browse.php?cat=<?= urlencode($c) ?>" 
         class="badge-category text-decoration-none fs-6 px-4 py-2">
        <?= $c ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ── Featured Listings ── -->
<section class="container mb-5">
  <h2 class="section-title mb-4">Featured <span>Listings</span></h2>
  <div class="row g-4">
    <?php for($i=1;$i<=4;$i++): ?>
    <div class="col-sm-6 col-lg-3">
      <div class="card h-100">
        <img src="https://via.placeholder.com/400x200/9B59B6/white?text=Product+<?=$i?>"
             class="card-img-top" alt="Product">
        <div class="card-body">
          <span class="badge-category">Tech Crafts</span>
          <h6 class="mt-2 fw-bold">Sample Product <?= $i ?></h6>
          <p class="text-muted small">Handcrafted with love in SA 🇿🇦</p>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <span style="color:var(--purple);font-weight:700;">R<?= $i*50 ?>.00</span>
            <a href="listing.php?id=<?=$i?>" class="btn btn-primary btn-sm">View</a>
          </div>
        </div>
      </div>
    </div>
    <?php endfor; ?>
  </div>
  <div class="text-center mt-4">
    <a href="browse.php" class="btn btn-outline-primary">View All Listings</a>
  </div>
</section>

<!-- ── Why HerCraft ── -->
<section style="background:white;padding:60px 0;">
  <div class="container">
    <h2 class="section-title text-center mb-5">Why <span>HerCraft Hub?</span></h2>
    <div class="row g-4 text-center">
      <?php
        $features = [
          ["💜","Verified Sellers","Every seller is verified for your safety and peace of mind."],
          ["🔒","Secure Payments","South African payment gateways — pay safely, every time."],
          ["📱","Mobile Friendly","Built for South Africa — works great on any phone."],
          ["🚀","Easy to Sell","List your item in under 2 minutes. No experience needed."],
        ];
        foreach($features as $f):
      ?>
      <div class="col-sm-6 col-lg-3">
        <div class="card p-4 h-100 text-center">
          <div style="font-size:2.5rem;"><?= $f[0] ?></div>
          <h6 class="mt-3 fw-bold" style="color:var(--purple);"><?= $f[1] ?></h6>
          <p class="text-muted small mt-2"><?= $f[2] ?></p>
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
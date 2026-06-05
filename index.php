<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start(); 
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>HerCraft Hub – Home</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- ── Hero Section ── -->
<section class="page-header" style="padding:80px 0;">
  <div class="container text-center">
    <h1 style="font-size:2.8rem;">Where Women Create,<br>Connect &amp; Sell</h1>
    <p class="mt-3" style="font-size:1.1rem;opacity:0.85;max-width:560px;margin:0 auto;">
      South Africa's first C2C marketplace built for women selling handmade goods, tech crafts &amp; digital products.
    </p>
    <div class="mt-4 d-flex gap-3 justify-content-center flex-wrap">
      <a href="browse.php" class="btn btn-outline-light px-4 py-2"
         style="border-radius:4px;letter-spacing:0.08em;font-size:0.85rem;text-transform:uppercase;">
        Browse Listings
      </a>
      <a href="register.php" class="btn btn-light px-4 py-2"
         style="border-radius:4px;letter-spacing:0.08em;font-size:0.85rem;
                text-transform:uppercase;color:var(--purple);font-weight:600;">
        Start Selling
      </a>
    </div>
  </div>
</section>

<!-- ── Category Pills ── -->
<section class="container my-5">
  <h2 class="section-title text-center mb-2">Shop by <span>Category</span></h2>
  <div class="section-divider"></div>
  <div class="d-flex flex-wrap justify-content-center gap-3">
    <?php
      $cats = ["Tech Crafts","Handmade","Digital Art","Accessories","Bundles","Beauty Tech"];
      foreach($cats as $c):
    ?>
      <a href="browse.php?cat=<?= urlencode($c) ?>"
         class="badge-category text-decoration-none">
        <?= $c ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ── Featured Listings ── -->
<section class="container mb-5">
  <h2 class="section-title mb-2">Featured <span>Listings</span></h2>
  <div class="section-divider" style="margin:12px 0 24px;"></div>
  <div class="row g-4">
    <?php for($i=1;$i<=4;$i++): ?>
    <div class="col-sm-6 col-lg-3">
      <div class="card h-100">
        <img src="https://via.placeholder.com/400x200/2D1C42/F5F0E8?text=Product+<?=$i?>"
             class="card-img-top" alt="Product <?= $i ?>">
        <div class="card-body">
          <span class="badge-category">Tech Crafts</span>
          <h6 class="mt-2 fw-bold" style="font-family:var(--font-head);font-size:1.05rem;">
            Sample Product <?= $i ?>
          </h6>
          <p class="small mt-1" style="color:var(--text-muted);">Handcrafted in South Africa</p>
          <div class="d-flex justify-content-between align-items-center mt-3">
            <span style="color:var(--purple);font-weight:700;font-family:var(--font-head);font-size:1.1rem;">
              R<?= $i*50 ?>.00
            </span>
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
<section style="background:var(--cream-dark);padding:60px 0;">
  <div class="container">
    <h2 class="section-title text-center mb-2">Why <span>HerCraft Hub?</span></h2>
    <div class="section-divider"></div>
    <div class="row g-4 text-center">
      <?php
        $features = [
          ["icon"=>"shield",        "title"=>"Verified Sellers", "desc"=>"Every seller is verified for your safety and peace of mind."],
          ["icon"=>"lock",          "title"=>"Secure Payments",  "desc"=>"South African payment gateways — pay safely, every time."],
          ["icon"=>"device-mobile", "title"=>"Mobile Friendly",  "desc"=>"Built for South Africa — works great on any phone."],
          ["icon"=>"rocket",        "title"=>"Easy to Sell",     "desc"=>"List your item in under 2 minutes. No experience needed."],
        ];
        foreach($features as $f):
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

<!-- ── Women in Tech Spotlight ── -->
<section class="container my-5">
  <h2 class="section-title text-center mb-2">Women in <span>Tech Spotlight</span></h2>
  <div class="section-divider"></div>
  <div class="row g-4 justify-content-center">
    <?php
      $spotlights = [
        ["N", "Naledi M.",    "Johannesburg", "Custom PCB jewellery and wearable tech accessories."],
        ["A", "Ayasha K.",    "Cape Town",    "Digital planners and Notion templates for creatives."],
        ["T", "Thandi B.",    "Pretoria",     "Handmade macrame and tech cable organisers."],
      ];
      foreach($spotlights as $s):
    ?>
    <div class="col-sm-6 col-lg-4">
      <div class="card p-4 text-center h-100">
        <div class="user-avatar mx-auto mb-3"
             style="width:56px;height:56px;font-size:1.4rem;border-width:2px;">
          <?= $s[0] ?>
        </div>
        <h6 class="fw-bold" style="font-family:var(--font-head);font-size:1.1rem;">
          <?= $s[1] ?>
        </h6>
        <small style="color:var(--text-muted);letter-spacing:0.06em;text-transform:uppercase;font-size:0.7rem;">
          <?= $s[2] ?>
        </small>
        <p class="mt-2 small" style="color:var(--text-muted);"><?= $s[3] ?></p>
        <a href="browse.php" class="btn btn-outline-primary btn-sm mt-auto">
          View Listings
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
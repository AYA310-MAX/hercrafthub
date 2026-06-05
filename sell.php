<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sell an Item – HerCraft Hub</title>
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

<section style="background:linear-gradient(135deg,#6B2D8B,#E91E8C);padding:40px 0;color:white;">
  <div class="container">
    <h1 style="font-weight:700;">Sell Your Item ✦</h1>
    <p style="opacity:0.9;">List your handmade good, tech craft or digital product in minutes</p>
  </div>
</section>

<div class="container my-5" style="max-width:700px;">
  <div class="card p-4 p-md-5">

    <?php if(isset($_SESSION['error'])): ?>
      <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <form action="php/sell_action.php" method="POST" enctype="multipart/form-data" id="sellForm">

      <div class="mb-3">
        <label class="form-label">Item Title</label>
        <input type="text" name="title" class="form-control"
               placeholder="e.g. Handmade Beaded Phone Case" 
               maxlength="100" required>
        <small class="text-muted"><span id="titleCount">0</span>/100 characters</small>
      </div>

      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-select" required>
          <option value="">-- Choose a category --</option>
          <option value="Tech Crafts">💻 Tech Crafts</option>
          <option value="Handmade">🧵 Handmade</option>
          <option value="Digital Art">🎨 Digital Art</option>
          <option value="Accessories">📱 Accessories</option>
          <option value="Bundles">📦 Bundles</option>
          <option value="Beauty Tech">✨ Beauty Tech</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4"
                  placeholder="Describe your item — materials, size, what makes it special..."
                  maxlength="500" required></textarea>
        <small class="text-muted"><span id="descCount">0</span>/500 characters</small>
      </div>

      <div class="row g-3 mb-3">
        <div class="col-sm-6">
          <label class="form-label">Price (ZAR)</label>
          <div class="input-group">
            <span class="input-group-text">R</span>
            <input type="number" name="price" class="form-control"
                   placeholder="0.00" min="1" step="0.01" required>
          </div>
        </div>
        <div class="col-sm-6">
          <label class="form-label">Condition</label>
          <select name="condition" class="form-select" required>
            <option value="">-- Select --</option>
            <option value="New">New</option>
            <option value="Like New">Like New</option>
            <option value="Good">Good</option>
            <option value="Fair">Fair</option>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control"
               placeholder="e.g. Johannesburg, Gauteng">
      </div>

      <div class="mb-4">
        <label class="form-label">Product Image</label>
        <input type="file" name="image" id="imageInput" class="form-control"
               accept="image/*">
        <small class="text-muted">JPG, PNG or GIF — max 2MB</small>

        <!-- Image preview -->
        <div id="imagePreview" class="mt-3 d-none">
          <img id="previewImg" src="" alt="Preview"
               style="max-width:100%;border-radius:12px;max-height:250px;object-fit:cover;">
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2">
        ✦ Post My Listing
      </button>

    </form>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script src="js/sell.js"></script>
</body>
</html>
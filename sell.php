<?php

session_start();

require_once 'config/db.php';

require_once 'includes/helpers.php';

require_once 'includes/auth.php';



if (!isset($_SESSION['user_id'])) {

    $_SESSION['redirect_after_login'] = 'sell.php';

    $_SESSION['error'] = 'Please log in or create an account to sell an item.';

    redirect_to('login.php');

}



require_seller();



$user_id = (int) $_SESSION['user_id'];

$user    = db_fetch_one(

    $conn,

    'SELECT full_name, location, bio FROM users WHERE id = ? LIMIT 1',

    'i',

    [$user_id]

);



$profile_incomplete = $user === null

    || trim((string) ($user['location'] ?? '')) === '';



$categories = get_categories($conn);

?>

<!DOCTYPE html>

<html lang="en" data-theme="">

<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">

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

  <div class="container">

    <h1>Sell Your Item</h1>

    <p>List your handmade good, tech craft or digital product in minutes</p>

  </div>

</section>



<div class="container my-5" style="max-width:700px;">

  <div class="card p-4 p-md-5">

    <?= render_flash_messages() ?>



    <?php if ($profile_incomplete): ?>

    <div class="alert alert-danger mb-4">

      Please complete your profile before listing items.

      <a href="profile.php" class="alert-link">Add your location and details</a>.

    </div>

    <?php endif; ?>



    <form action="<?= htmlspecialchars(url_for('php/sell_action.php')) ?>" method="POST" enctype="multipart/form-data" id="sellForm"

          <?= $profile_incomplete ? 'onsubmit="return false;"' : '' ?>>



      <div class="mb-3">

        <label class="form-label">Item Title</label>

        <input type="text" name="title" class="form-control"

               placeholder="e.g. Handmade Beaded Phone Case"

               maxlength="100" required <?= $profile_incomplete ? 'disabled' : '' ?>>

        <small class="text-muted"><span id="titleCount">0</span>/100 characters</small>

      </div>



      <div class="mb-3">

        <label class="form-label">Category</label>

        <select name="category" class="form-select" required <?= $profile_incomplete ? 'disabled' : '' ?>>

          <option value="" disabled selected>Choose a category</option>

          <?php foreach ($categories as $cat): ?>

          <option value="<?= htmlspecialchars($cat['name']) ?>">

            <?= htmlspecialchars($cat['name']) ?>

          </option>

          <?php endforeach; ?>

        </select>

      </div>



      <div class="mb-3">

        <label class="form-label">Description</label>

        <textarea name="description" class="form-control" rows="4"

                  placeholder="Describe your item. Include materials, size, and what makes it special."

                  maxlength="500" required <?= $profile_incomplete ? 'disabled' : '' ?>></textarea>

        <small class="text-muted"><span id="descCount">0</span>/500 characters</small>

      </div>



      <div class="row g-3 mb-3">

        <div class="col-sm-4">

          <label class="form-label">Price (ZAR)</label>

          <div class="input-group">

            <span class="input-group-text input-group-currency">R</span>

            <input type="number" name="price" class="form-control"

                   placeholder="0.00" min="1" step="0.01" required <?= $profile_incomplete ? 'disabled' : '' ?>>

          </div>

        </div>

        <div class="col-sm-4">

          <label class="form-label">Quantity</label>

          <input type="number" name="quantity" class="form-control"

                 value="1" min="1" max="999" required <?= $profile_incomplete ? 'disabled' : '' ?>>

          <small class="text-muted">How many you have in stock</small>

        </div>

        <div class="col-sm-4">

          <label class="form-label">Condition</label>

          <select name="condition" class="form-select" required <?= $profile_incomplete ? 'disabled' : '' ?>>

            <option value="" disabled selected>Select condition</option>

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

               placeholder="e.g. Johannesburg, Gauteng"

               value="<?= htmlspecialchars((string) ($user['location'] ?? '')) ?>"

               <?= $profile_incomplete ? 'disabled' : '' ?>>

      </div>



      <div class="mb-4">

        <label class="form-label">Product Image</label>

        <input type="file" name="image" id="imageInput" class="form-control" accept="image/*"

               <?= $profile_incomplete ? 'disabled' : '' ?>>

        <small class="text-muted">JPG, PNG or GIF. Maximum 2MB.</small>

        <div id="imagePreview" class="mt-3 d-none">

          <img id="previewImg" src="" alt="Preview" class="sell-preview-img">

        </div>

      </div>



      <button type="submit" class="btn btn-primary w-100 py-2" <?= $profile_incomplete ? 'disabled' : '' ?>>

        Post My Listing

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


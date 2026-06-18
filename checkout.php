<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';
require_once 'includes/auth.php';

require_buyer();

$product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

if ($product_id <= 0) {
    $_SESSION['error'] = 'No item selected for checkout.';
    redirect_to('browse.php');
}

$product = db_fetch_one(
    $conn,
    'SELECT p.*, c.name AS category_name, u.full_name AS seller_name
     FROM products p
     INNER JOIN categories c ON p.category_id = c.id
     INNER JOIN users u ON p.seller_id = u.id
     WHERE p.id = ? AND ' . product_available_sql() . '
     LIMIT 1',
    'i',
    [$product_id]
);

if ($product === null) {
    $_SESSION['error'] = 'This item is no longer available.';
    redirect_to('browse.php');
}

if ((int) $product['seller_id'] === (int) $_SESSION['user_id']) {
    $_SESSION['error'] = 'You cannot purchase your own listing.';
    redirect_to('listing.php?id=' . $product_id);
}

$buyer = db_fetch_one(
    $conn,
    'SELECT full_name, location FROM users WHERE id = ? LIMIT 1',
    'i',
    [(int) $_SESSION['user_id']]
);

$item_price    = (float) $product['price'];
$delivery_fee  = delivery_fee_amount();
$charity_fee   = charity_donation_amount();
$default_total = $item_price + $delivery_fee;
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Checkout – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
  <a href="listing.php?id=<?= (int) $product['id'] ?>" class="back-btn">
    <i class="ti ti-arrow-left"></i> Back to listing
  </a>
</div>

<section class="page-header" style="padding:36px 0;">
  <div class="container">
    <h1>Checkout</h1>
    <p>Complete your delivery details and confirm your order</p>
  </div>
</section>

<div class="container my-5">
  <?= render_flash_messages() ?>

  <div class="row g-4">
    <div class="col-lg-7">
      <div class="card p-4">
        <h5 class="listing-title mb-4"><i class="ti ti-truck-delivery me-2"></i>Delivery Address</h5>
        <p class="text-muted small">Enter your exact delivery location so the seller knows where to send your item.</p>

        <form action="<?= htmlspecialchars(url_for('php/checkout_action.php')) ?>" method="POST" id="checkoutForm">
          <?= csrf_field() ?>
          <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">

          <div class="mb-3">
            <label class="form-label">Street Address</label>
            <input type="text" name="street" class="form-control" required
                   placeholder="For example 42 Main Road, Unit 3" maxlength="200">
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">City / Town</label>
              <input type="text" name="city" class="form-control" required
                     placeholder="For example Johannesburg" maxlength="100">
            </div>
            <div class="col-md-6">
              <label class="form-label">Province</label>
              <select name="province" class="form-select" required>
                <option value="" disabled selected>Select province</option>
                <?php foreach (sa_provinces() as $province): ?>
                <option value="<?= htmlspecialchars($province) ?>"><?= htmlspecialchars($province) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label">Postal Code</label>
            <input type="text" name="postal_code" class="form-control" required
                   placeholder="For example 2000" pattern="[0-9]{4}" maxlength="4"
                   title="Four digit South African postal code">
          </div>

          <h5 class="listing-title mb-3 mt-4"><i class="ti ti-credit-card me-2"></i>Payment Method</h5>
          <p class="text-muted small">Select your preferred payment method below. This is a visual presentation demo.</p>

          <div class="payment-methods mb-4">
            <div class="form-check border rounded p-3 mb-2">
              <div class="d-flex align-items-center">
                <input class="form-check-input mt-0 me-3 pay-radio" type="radio" name="payment_method" id="payApple" value="apple" checked>
                <label class="form-check-label d-flex align-items-center flex-grow-1" for="payApple" style="cursor: pointer; margin-left: 10px;">
                  <i class="ti ti-brand-apple fs-4 me-2"></i> Apple Pay
                </label>
              </div>
              <div class="payment-details mt-3 ps-4" id="details-apple">
                <button type="button" class="btn btn-dark w-100"><i class="ti ti-brand-apple"></i> Pay with Apple</button>
                <small class="text-muted mt-2 d-block text-center">You will be redirected to Apple to complete your purchase securely.</small>
              </div>
            </div>
            <div class="form-check border rounded p-3 mb-2">
              <div class="d-flex align-items-center">
                <input class="form-check-input mt-0 me-3 pay-radio" type="radio" name="payment_method" id="payGoogle" value="google">
                <label class="form-check-label d-flex align-items-center flex-grow-1" for="payGoogle" style="cursor: pointer; margin-left: 10px;">
                  <i class="ti ti-brand-google fs-4 me-2"></i> Google Pay
                </label>
              </div>
              <div class="payment-details mt-3 ps-4 d-none" id="details-google">
                <button type="button" class="btn btn-outline-dark w-100"><i class="ti ti-brand-google"></i> Pay with Google</button>
                <small class="text-muted mt-2 d-block text-center">You will be redirected to Google to complete your purchase securely.</small>
              </div>
            </div>
            <div class="form-check border rounded p-3 mb-2">
              <div class="d-flex align-items-center">
                <input class="form-check-input mt-0 me-3 pay-radio" type="radio" name="payment_method" id="payPaypal" value="paypal">
                <label class="form-check-label d-flex align-items-center flex-grow-1" for="payPaypal" style="cursor: pointer; margin-left: 10px;">
                  <i class="ti ti-brand-paypal fs-4 me-2"></i> Paypal
                </label>
              </div>
              <div class="payment-details mt-3 ps-4 d-none" id="details-paypal">
                <button type="button" class="btn btn-primary w-100" style="background:#003087;border:none;"><i class="ti ti-brand-paypal"></i> Log In to PayPal</button>
                <small class="text-muted mt-2 d-block text-center">You will be redirected to PayPal to complete your purchase securely.</small>
              </div>
            </div>
            <div class="form-check border rounded p-3 mb-2">
              <div class="d-flex align-items-center">
                <input class="form-check-input mt-0 me-3 pay-radio" type="radio" name="payment_method" id="payCard" value="card">
                <label class="form-check-label d-flex align-items-center flex-grow-1" for="payCard" style="cursor: pointer; margin-left: 10px;">
                  <i class="ti ti-credit-card fs-4 me-2"></i> Credit or Debit Card
                </label>
              </div>
              <div class="payment-details mt-3 ps-4 d-none" id="details-card">
                <div class="mb-2">
                  <input type="text" class="form-control" placeholder="Card Number" value="4111 1111 1111 1111">
                </div>
                <div class="row g-2">
                  <div class="col-6">
                    <input type="text" class="form-control" placeholder="MM/YY" value="12/26">
                  </div>
                  <div class="col-6">
                    <input type="text" class="form-control" placeholder="CVV" value="123">
                  </div>
                </div>
              </div>
            </div>
            <div class="form-check border rounded p-3 mb-2">
              <div class="d-flex align-items-center">
                <input class="form-check-input mt-0 me-3 pay-radio" type="radio" name="payment_method" id="paySamsung" value="samsung">
                <label class="form-check-label d-flex align-items-center flex-grow-1" for="paySamsung" style="cursor: pointer; margin-left: 10px;">
                  <i class="ti ti-device-mobile fs-4 me-2"></i> Samsung Pay
                </label>
              </div>
              <div class="payment-details mt-3 ps-4 d-none" id="details-samsung">
                <button type="button" class="btn btn-outline-primary w-100"><i class="ti ti-device-mobile"></i> Samsung Pay via App</button>
                <small class="text-muted mt-2 d-block text-center">Open your Samsung Pay app to confirm.</small>
              </div>
            </div>
          </div>

          <div class="card p-3 mb-4 checkout-charity-card">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="charity_donation" value="1"
                     id="charityDonation">
              <label class="form-check-label" for="charityDonation">
                <strong>Add <?= format_price($charity_fee) ?> charity donation</strong>
                <br><small class="text-muted">Support women led community initiatives across South Africa.</small>
              </label>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2" id="placeOrderBtn">
            <i class="ti ti-lock me-2"></i>Place Order <span id="orderTotalLabel"><?= format_price($default_total) ?></span>
          </button>
        </form>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card p-4 checkout-summary">
        <h5 class="listing-title mb-3">Order Summary</h5>
        <div class="d-flex gap-3 mb-3">
          <img src="<?= htmlspecialchars(product_image_src($product['image'])) ?>"
               alt="<?= htmlspecialchars($product['title']) ?>"
               class="dashboard-thumb">
          <div>
            <span class="badge-category"><?= htmlspecialchars($product['category_name']) ?></span>
            <h6 class="mt-1 listing-title"><?= htmlspecialchars($product['title']) ?></h6>
            <small class="text-muted">Sold by <?= htmlspecialchars($product['seller_name']) ?></small>
          </div>
        </div>
        <hr>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Item price</span>
          <span id="summaryItem"><?= format_price($item_price) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2">
          <span class="text-muted">Delivery fee</span>
          <span id="summaryDelivery"><?= format_price($delivery_fee) ?></span>
        </div>
        <div class="d-flex justify-content-between mb-2 d-none" id="summaryCharityRow">
          <span class="text-muted">Charity donation</span>
          <span id="summaryCharity"><?= format_price($charity_fee) ?></span>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
          <strong>Total</strong>
          <strong class="listing-price" id="summaryTotal"><?= format_price($default_total) ?></strong>
        </div>
        <p class="text-muted small mt-3 mb-0">
          <i class="ti ti-shield-check me-1"></i> Secure checkout. Session expires after 30 minutes of inactivity.
        </p>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
(function () {
  const itemPrice    = <?= json_encode($item_price) ?>;
  const deliveryFee  = <?= json_encode($delivery_fee) ?>;
  const charityFee   = <?= json_encode($charity_fee) ?>;
  const charityCheck = document.getElementById('charityDonation');
  const charityRow   = document.getElementById('summaryCharityRow');
  const fmt = (n) => 'R' + n.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');

  function updateTotals() {
    const charity = charityCheck.checked ? charityFee : 0;
    const total   = itemPrice + deliveryFee + charity;
    charityRow.classList.toggle('d-none', !charityCheck.checked);
    document.getElementById('summaryTotal').textContent     = fmt(total);
    document.getElementById('orderTotalLabel').textContent    = fmt(total);
  }

  charityCheck.addEventListener('change', updateTotals);

  // Payment mock toggle
  const payRadios = document.querySelectorAll('.pay-radio');
  payRadios.forEach(radio => {
    radio.addEventListener('change', function() {
      document.querySelectorAll('.payment-details').forEach(el => el.classList.add('d-none'));
      document.getElementById('details-' + this.value).classList.remove('d-none');
    });
  });
})();
</script>
</body>
</html>

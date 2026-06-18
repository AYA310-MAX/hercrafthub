<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

require_buyer();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('browse.php');
}

if (!rate_limit_allow('checkout', 8, 3600)) {
    $_SESSION['error'] = 'Too many checkout attempts. Please wait an hour and try again.';
    redirect_to('browse.php');
}

if (!verify_csrf($_POST['csrf_token'] ?? null)) {
    $_SESSION['error'] = 'Security check failed. Please try again.';
    redirect_to('browse.php');
}

$product_id   = (int) ($_POST['product_id'] ?? 0);
$street       = trim($_POST['street'] ?? '');
$city         = trim($_POST['city'] ?? '');
$province     = trim($_POST['province'] ?? '');
$postal_code  = trim($_POST['postal_code'] ?? '');
$add_charity  = isset($_POST['charity_donation']);
$buyer_id     = (int) $_SESSION['user_id'];

if ($product_id <= 0 || $street === '' || $city === '' || $province === '' || $postal_code === '') {
    $_SESSION['error'] = 'Please complete all delivery fields.';
    redirect_to('checkout.php?product_id=' . $product_id);
}

if (!preg_match('/^\d{4}$/', $postal_code)) {
    $_SESSION['error'] = 'Please enter a valid 4-digit postal code.';
    redirect_to('checkout.php?product_id=' . $product_id);
}

if (!in_array($province, sa_provinces(), true)) {
    $_SESSION['error'] = 'Please select a valid province.';
    redirect_to('checkout.php?product_id=' . $product_id);
}

$product = db_fetch_one(
    $conn,
    'SELECT id, seller_id, title, price, quantity, is_active, is_sold
     FROM products
     WHERE id = ? LIMIT 1',
    'i',
    [$product_id]
);

if ($product === null) {
    $_SESSION['error'] = 'This listing is no longer available.';
    redirect_to('browse.php');
}

if ((int) $product['seller_id'] === $buyer_id) {
    $_SESSION['error'] = 'You cannot purchase your own listing.';
    redirect_to('listing.php?id=' . $product_id);
}

if (!(int) $product['is_active'] || (int) $product['is_sold'] || (int) $product['quantity'] < 1) {
    $_SESSION['error'] = 'This item is sold out or no longer available.';
    redirect_to('browse.php');
}

$delivery_address = $street . ', ' . $city . ', ' . $province . ' ' . $postal_code;
$item_total       = (float) $product['price'];
$delivery_fee     = delivery_fee_amount();
$charity_amount   = $add_charity ? charity_donation_amount() : 0.0;
$total_amount     = $item_total + $delivery_fee + $charity_amount;
$seller_id        = (int) $product['seller_id'];
$new_quantity     = (int) $product['quantity'] - 1;
$mark_sold        = $new_quantity <= 0 ? 1 : 0;

$conn->begin_transaction();

try {
    $updated = db_execute(
        $conn,
        'UPDATE products SET quantity = ?, is_sold = ? WHERE id = ? AND seller_id = ? AND quantity > 0 AND is_sold = 0',
        'iiii',
        [$new_quantity, $mark_sold, $product_id, $seller_id]
    );

    if (!$updated || $conn->affected_rows === 0) {
        throw new RuntimeException('Stock unavailable');
    }

    $inserted = db_execute(
        $conn,
        'INSERT INTO sales (product_id, seller_id, buyer_id, quantity, unit_price, item_total,
                            delivery_address, delivery_fee, charity_donation, total_amount)
         VALUES (?, ?, ?, 1, ?, ?, ?, ?, ?, ?)',
        'iiiddsddd',
        [
            $product_id,
            $seller_id,
            $buyer_id,
            $item_total,
            $item_total,
            $delivery_address,
            $delivery_fee,
            $charity_amount,
            $total_amount,
        ]
    );

    if (!$inserted) {
        throw new RuntimeException('Order record failed');
    }

    db_execute(
        $conn,
        'DELETE FROM wishlists WHERE user_id = ? AND product_id = ?',
        'ii',
        [$buyer_id, $product_id]
    );

    $conn->commit();

    $_SESSION['success'] = 'Order placed! "' . $product['title'] . '" will be delivered to '
        . $delivery_address . '. Total paid: ' . format_price($total_amount) . '.';
    redirect_to('dashboard.php');
} catch (Throwable $e) {
    $conn->rollback();
    $_SESSION['error'] = 'Unable to complete checkout. The item may have just sold out.';
    redirect_to('checkout.php?product_id=' . $product_id);
}

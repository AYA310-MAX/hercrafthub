<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$product_id = (int) ($_POST['product_id'] ?? $_GET['product_id'] ?? 0);

if ($_SESSION['role'] === 'seller') {
    $_SESSION['error'] = 'Seller accounts cannot purchase items. Create a separate Buyer account to shop.';
    redirect_to('register.php');
}

if ($product_id <= 0) {
    redirect_to('browse.php');
}

redirect_to('checkout.php?product_id=' . $product_id);

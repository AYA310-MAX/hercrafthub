<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

if ($_SESSION['role'] !== 'seller' && $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Only sellers can manage listings.';
    redirect_to('dashboard.php');
}

$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$action = $_GET['action'] ?? '';
$user_id = (int) $_SESSION['user_id'];

if ($id <= 0 || !in_array($action, ['toggle', 'delete'], true)) {
    $_SESSION['error'] = 'Invalid listing action.';
    redirect_to('dashboard.php');
}

if ($action === 'delete' && !rate_limit_allow('listing_delete', 10, 3600)) {
    $_SESSION['error'] = 'Too many delete requests. Please try again later.';
    redirect_to('dashboard.php');
}

$product = assert_product_owner($conn, $id, $user_id);

if ($product === null) {
    $_SESSION['error'] = 'You do not have permission to manage this listing. Sellers can only edit their own items.';
    redirect_to('dashboard.php');
}

if ($action === 'toggle') {
    $updated = db_execute(
        $conn,
        'UPDATE products SET is_active = IF(is_active = 1, 0, 1) WHERE id = ? AND seller_id = ?',
        'ii',
        [$id, $user_id]
    );
    $_SESSION['success'] = $updated ? 'Listing visibility updated.' : 'Unable to update listing visibility.';
}

if ($action === 'delete') {
    $deleted = db_execute(
        $conn,
        'DELETE FROM products WHERE id = ? AND seller_id = ?',
        'ii',
        [$id, $user_id]
    );
    if ($deleted && $conn->affected_rows > 0) {
        delete_product_image($product['image']);
        $_SESSION['success'] = 'Listing deleted successfully.';
    } else {
        $_SESSION['error'] = 'Unable to delete listing.';
    }
}

redirect_to('dashboard.php');

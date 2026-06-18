<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$product_id = (int) ($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
$action     = $_POST['action'] ?? $_GET['action'] ?? 'toggle';
$user_id    = (int) $_SESSION['user_id'];
$is_ajax    = ($_SERVER['REQUEST_METHOD'] === 'POST');

if ($product_id <= 0) {
    if ($is_ajax) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['success' => false]);
        exit;
    }
    $_SESSION['error'] = 'Invalid product selected.';
    header('Location: ../browse.php');
    exit;
}

$existing = db_fetch_one(
    $conn,
    'SELECT id FROM wishlists WHERE user_id = ? AND product_id = ? LIMIT 1',
    'ii',
    [$user_id, $product_id]
);

if ($action === 'remove' || ($existing !== null && $action === 'toggle')) {
    db_execute(
        $conn,
        'DELETE FROM wishlists WHERE user_id = ? AND product_id = ?',
        'ii',
        [$user_id, $product_id]
    );
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'saved' => false]);
        exit;
    }
    $_SESSION['success'] = 'Item removed from your wishlist.';
} else {
    if ($existing === null) {
        db_execute(
            $conn,
            'INSERT INTO wishlists (user_id, product_id) VALUES (?, ?)',
            'ii',
            [$user_id, $product_id]
        );
    }
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'saved' => true]);
        exit;
    }
    $_SESSION['success'] = 'Item saved to your wishlist.';
}

$redirect = $_GET['redirect'] ?? 'listing';
if ($redirect === 'dashboard') {
    header('Location: ../dashboard.php');
} else {
    header('Location: ../listing.php?id=' . $product_id);
}

exit;

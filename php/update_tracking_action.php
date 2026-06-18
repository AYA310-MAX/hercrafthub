<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

require_seller();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('dashboard.php');
}

$seller_id = (int) $_SESSION['user_id'];
$sale_id   = (int) ($_POST['sale_id'] ?? 0);
$status    = trim($_POST['tracking_status'] ?? '');

$allowed_statuses = ['Processing', 'Shipped', 'In Transit', 'Delivered'];

if ($sale_id <= 0 || !in_array($status, $allowed_statuses, true)) {
    $_SESSION['error'] = 'Invalid tracking status selected.';
    redirect_to('dashboard.php');
}

// Verify this sale belongs to this seller
$sale = db_fetch_one($conn, 'SELECT id FROM sales WHERE id = ? AND seller_id = ? LIMIT 1', 'ii', [$sale_id, $seller_id]);

if ($sale === null) {
    $_SESSION['error'] = 'Order not found or unauthorized.';
    redirect_to('dashboard.php');
}

$updated = db_execute(
    $conn,
    'UPDATE sales SET tracking_status = ? WHERE id = ?',
    'si',
    [$status, $sale_id]
);

if ($updated) {
    $_SESSION['success'] = 'Tracking status updated to ' . htmlspecialchars($status) . '.';
} else {
    $_SESSION['error'] = 'Failed to update tracking status.';
}

redirect_to('dashboard.php');

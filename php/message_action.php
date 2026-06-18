<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit;
}

$sender_id   = (int) $_SESSION['user_id'];
$receiver_id = (int) ($_POST['receiver_id'] ?? 0);
$product_id  = (int) ($_POST['product_id'] ?? 0);
$body        = trim($_POST['body'] ?? '');

if ($receiver_id <= 0 || $body === '' || $product_id <= 0) {
    $_SESSION['error'] = 'Please enter a valid message.';
    header('Location: ../message.php?product_id=' . $product_id);
    exit;
}

if ($sender_id === $receiver_id) {
    $_SESSION['error'] = 'You cannot send a message to yourself.';
    header('Location: ../listing.php?id=' . $product_id);
    exit;
}

$inserted = db_execute(
    $conn,
    'INSERT INTO messages (sender_id, receiver_id, product_id, body) VALUES (?, ?, ?, ?)',
    'iiis',
    [$sender_id, $receiver_id, $product_id, $body]
);

if ($inserted) {
    $_SESSION['success'] = 'Your message has been sent successfully.';
    header('Location: ../listing.php?id=' . $product_id);
} else {
    $_SESSION['error'] = 'Unable to send your message. Please try again.';
    header('Location: ../message.php?product_id=' . $product_id);
}

exit;

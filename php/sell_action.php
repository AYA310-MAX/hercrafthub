<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

require_seller();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../sell.php');
    exit;
}

$seller_id   = (int) $_SESSION['user_id'];
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$price       = (float) ($_POST['price'] ?? 0);
$condition   = trim($_POST['condition'] ?? '');
$location    = trim($_POST['location'] ?? '');
$category    = trim($_POST['category'] ?? '');
$quantity    = (int) ($_POST['quantity'] ?? 1);

if ($title === '' || $description === '' || $price <= 0 || $condition === '' || $category === '' || $quantity < 1) {
    $_SESSION['error'] = 'Please fill in all required fields.';
    header('Location: ../sell.php');
    exit;
}

$allowed_conditions = ['New', 'Like New', 'Good', 'Fair'];
if (!in_array($condition, $allowed_conditions, true)) {
    $_SESSION['error'] = 'Please select a valid condition.';
    header('Location: ../sell.php');
    exit;
}

$category_row = db_fetch_one($conn, 'SELECT id FROM categories WHERE name = ? LIMIT 1', 's', [$category]);
if ($category_row === null) {
    $_SESSION['error'] = 'Please select a valid category.';
    header('Location: ../sell.php');
    exit;
}

$category_id = (int) $category_row['id'];
$image_name  = null;

$user_row = db_fetch_one($conn, 'SELECT location FROM users WHERE id = ? LIMIT 1', 'i', [$seller_id]);
if ($user_row !== null && trim((string) ($user_row['location'] ?? '')) === '') {
    $_SESSION['error'] = 'Please complete your profile with a location before listing items.';
    header('Location: ../profile.php');
    exit;
}

if ($location !== '') {
    db_execute($conn, 'UPDATE users SET location = ? WHERE id = ?', 'si', [$location, $seller_id]);
}

if (!empty($_FILES['image']['name'])) {
    $image_name = save_product_image($_FILES['image']);
    if ($image_name === null) {
        $_SESSION['error'] = 'Image upload failed. Use JPG, PNG, or GIF under 2MB.';
        header('Location: ../sell.php');
        exit;
    }
}

$inserted = db_execute(
    $conn,
    'INSERT INTO products (seller_id, category_id, title, description, price, image, condition_type, location, quantity)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
    'iissdsssi',
    [$seller_id, $category_id, $title, $description, $price, $image_name, $condition, $location, $quantity]
);

if ($inserted) {
    $_SESSION['success'] = 'Your listing has been posted successfully.';
    header('Location: ../dashboard.php');
} else {
    if ($image_name !== null) {
        delete_product_image($image_name);
    }
    $_SESSION['error'] = 'Something went wrong. Please try again.';
    header('Location: ../sell.php');
}

exit;

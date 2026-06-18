<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

if ($_SESSION['role'] !== 'seller' && $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = 'Only sellers can update listings.';
    header('Location: ../dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../dashboard.php');
    exit;
}

$id          = (int) ($_POST['id'] ?? 0);
$title       = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$price       = (float) ($_POST['price'] ?? 0);
$condition   = trim($_POST['condition'] ?? '');
$location    = trim($_POST['location'] ?? '');
$category    = trim($_POST['category'] ?? '');
$quantity    = (int) ($_POST['quantity'] ?? 1);
$user_id     = (int) $_SESSION['user_id'];

if ($id <= 0 || $title === '' || $description === '' || $price <= 0 || $condition === '' || $category === '' || $quantity < 0) {
    $_SESSION['error'] = 'Please fill in all required fields.';
    header('Location: ../edit_listing.php?id=' . $id);
    exit;
}

$existing = db_fetch_one(
    $conn,
    'SELECT id, image FROM products WHERE id = ? AND seller_id = ? LIMIT 1',
    'ii',
    [$id, $user_id]
);

if ($existing === null) {
    $_SESSION['error'] = 'You do not have permission to edit this listing.';
    header('Location: ../dashboard.php');
    exit;
}

$category_row = db_fetch_one($conn, 'SELECT id FROM categories WHERE name = ? LIMIT 1', 's', [$category]);
if ($category_row === null) {
    $_SESSION['error'] = 'Please select a valid category.';
    header('Location: ../edit_listing.php?id=' . $id);
    exit;
}

$category_id = (int) $category_row['id'];
$image_name  = $existing['image'];

if (!empty($_FILES['image']['name'])) {
    $uploaded = save_product_image($_FILES['image']);
    if ($uploaded === null) {
        $_SESSION['error'] = 'Image upload failed. Use JPG, PNG, or GIF under 2MB.';
        header('Location: ../edit_listing.php?id=' . $id);
        exit;
    }
    delete_product_image($existing['image']);
    $image_name = $uploaded;
}

$is_sold = $quantity < 1 ? 1 : 0;

$updated = db_execute(
    $conn,
    'UPDATE products
     SET category_id = ?, title = ?, description = ?, price = ?, image = ?,
         condition_type = ?, location = ?, quantity = ?, is_sold = ?
     WHERE id = ? AND seller_id = ?',
    'issdsssiii',
    [$category_id, $title, $description, $price, $image_name, $condition, $location, $quantity, $is_sold, $id, $user_id]
);

if ($updated) {
    $_SESSION['success'] = 'Listing updated successfully.';
    header('Location: ../dashboard.php');
} else {
    $_SESSION['error'] = 'Unable to update listing. Please try again.';
    header('Location: ../edit_listing.php?id=' . $id);
}

exit;

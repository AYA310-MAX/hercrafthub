<?php
session_start();
require '../config/db.php';

// ── Must be logged in ──
if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "Please log in to post a listing.";
  header('Location: ../login.php');
  exit;
}

// ── Must be a seller ──
if ($_SESSION['role'] === 'buyer') {
  $_SESSION['error'] = "Please upgrade to a Seller account to list items.";
  header('Location: ../sell.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../sell.php');
  exit;
}

// ── Collect inputs ──
$seller_id   = $_SESSION['user_id'];
$title       = trim($_POST['title']);
$description = trim($_POST['description']);
$price       = floatval($_POST['price']);
$condition   = $_POST['condition'];
$location    = trim($_POST['location']);
$category    = trim($_POST['category']);

// ── Validation ──
if (empty($title) || empty($description) || $price <= 0) {
  $_SESSION['error'] = "Please fill in all required fields.";
  header('Location: ../sell.php');
  exit;
}

// ── Get category id ──
$cat_stmt = $conn->prepare("SELECT id FROM categories WHERE name = ?");
$cat_stmt->bind_param("s", $category);
$cat_stmt->execute();
$cat_stmt->bind_result($category_id);
$cat_stmt->fetch();
$cat_stmt->close();

// ── Handle image upload ──
$image_name = null;

if (!empty($_FILES['image']['name'])) {
  $allowed   = ['jpg','jpeg','png','gif'];
  $ext       = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
  $max_size  = 2 * 1024 * 1024; // 2MB

  if (!in_array($ext, $allowed)) {
    $_SESSION['error'] = "Only JPG, PNG or GIF images are allowed.";
    header('Location: ../sell.php');
    exit;
  }

  if ($_FILES['image']['size'] > $max_size) {
    $_SESSION['error'] = "Image must be under 2MB.";
    header('Location: ../sell.php');
    exit;
  }

  $image_name = uniqid('product_') . '.' . $ext;
  move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/' . $image_name);
}

// ── Insert product ──
$stmt = $conn->prepare(
  "INSERT INTO products 
   (seller_id, category_id, title, description, price, image, condition_type, location) 
   VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
  "iissdss s",
  $seller_id, $category_id, $title, $description,
  $price, $image_name, $condition, $location
);

if ($stmt->execute()) {
  $_SESSION['success'] = "Your listing has been posted successfully! ✦";
  header('Location: ../browse.php');
} else {
  $_SESSION['error'] = "Something went wrong. Please try again.";
  header('Location: ../sell.php');
}

$stmt->close();
$conn->close();
?>

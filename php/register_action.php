<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../register.php');
  exit;
}

// ── Collect and sanitize inputs ──
$full_name = trim($_POST['full_name']);
$email     = trim($_POST['email']);
$password  = $_POST['password'];
$confirm   = $_POST['confirm_password'];
$role      = $_POST['role'];

// ── Validation ──
if (empty($full_name) || empty($email) || empty($password) || empty($role)) {
  $_SESSION['error'] = "Please fill in all fields.";
  header('Location: ../register.php');
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $_SESSION['error'] = "Please enter a valid email address.";
  header('Location: ../register.php');
  exit;
}

if (strlen($password) < 6) {
  $_SESSION['error'] = "Password must be at least 6 characters.";
  header('Location: ../register.php');
  exit;
}

if ($password !== $confirm) {
  $_SESSION['error'] = "Passwords do not match.";
  header('Location: ../register.php');
  exit;
}

if (!in_array($role, ['buyer','seller'])) {
  $_SESSION['error'] = "Invalid role selected.";
  header('Location: ../register.php');
  exit;
}

// ── Check if email already exists ──
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
  $_SESSION['error'] = "An account with that email already exists.";
  header('Location: ../register.php');
  exit;
}
$stmt->close();

// ── Hash password and insert user ──
$hashed = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare(
  "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("ssss", $full_name, $email, $hashed, $role);

if ($stmt->execute()) {
  $_SESSION['user_id']   = $stmt->insert_id;
  $_SESSION['full_name'] = $full_name;
  $_SESSION['role']      = $role;
  $_SESSION['success']   = "Welcome to HerCraft Hub, $full_name! 🎉";
  header('Location: ../index.php');
} else {
  $_SESSION['error'] = "Something went wrong. Please try again.";
  header('Location: ../register.php');
}

$stmt->close();
$conn->close();
?>
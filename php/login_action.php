<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../login.php');
  exit;
}

$email    = trim($_POST['email']);
$password = $_POST['password'];

// ── Validation ──
if (empty($email) || empty($password)) {
  $_SESSION['error'] = "Please enter your email and password.";
  header('Location: ../login.php');
  exit;
}

// ── Find user by email ──
$stmt = $conn->prepare(
  "SELECT id, full_name, password, role, is_active FROM users WHERE email = ?"
);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($id, $full_name, $hashed, $role, $is_active);
$stmt->fetch();
$stmt->close();

// ── Check password ──
if (!$id ) {
  $_SESSION['error'] = "Incorrect email or password. Please try again.";
  header('Location: ../login.php');
  exit;
}

$hashed = (string)$hashed;

if (!password_verify($password, $hashed)) {
  $_SESSION['error'] = "Incorrect email or password. Please try again.";
  header('Location: ../login.php');
  exit;
}

// ── Check account is active ──
if (!$is_active) {
  $_SESSION['error'] = "Your account has been suspended. Please contact support.";
  header('Location: ../login.php');
  exit;
}

// ── Set session and redirect by role ──
$_SESSION['user_id']   = $id;
$_SESSION['full_name'] = $full_name;
$_SESSION['role']      = $role;

if ($role === 'admin') {
  header('Location: ../admin/index.php');
} else {
  header('Location: ../index.php');
}

$conn->close();
exit;
?>
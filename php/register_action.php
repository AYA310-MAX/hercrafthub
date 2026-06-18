<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$full_name = trim($_POST['full_name'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = $_POST['password'] ?? '';
$confirm   = $_POST['confirm_password'] ?? '';
$role      = $_POST['role'] ?? '';
$location  = trim($_POST['location'] ?? '');

if ($full_name === '' || $email === '' || $password === '' || $role === '') {
    $_SESSION['error'] = 'Please fill in all fields.';
    header('Location: ../register.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email address.';
    header('Location: ../register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password must be at least 6 characters.';
    header('Location: ../register.php');
    exit;
}

if ($password !== $confirm) {
    $_SESSION['error'] = 'Passwords do not match.';
    header('Location: ../register.php');
    exit;
}

if (!in_array($role, ['buyer', 'seller'], true)) {
    $_SESSION['error'] = 'Invalid role selected.';
    header('Location: ../register.php');
    exit;
}

$existing = db_fetch_one($conn, 'SELECT id FROM users WHERE email = ? LIMIT 1', 's', [$email]);
if ($existing !== null) {
    $_SESSION['error'] = 'An account with that email already exists.';
    header('Location: ../register.php');
    exit;
}

$hashed = password_hash($password, PASSWORD_BCRYPT);

$inserted = db_execute(
    $conn,
    'INSERT INTO users (full_name, email, password, role, location) VALUES (?, ?, ?, ?, ?)',
    'sssss',
    [$full_name, $email, $hashed, $role, $location !== '' ? $location : null]
);

if (!$inserted) {
    $_SESSION['error'] = 'Something went wrong. Please try again.';
    header('Location: ../register.php');
    exit;
}

$user_id = db_last_insert_id($conn);
set_user_session($user_id, $full_name, $role, null);
$_SESSION['success'] = 'Welcome to HerCraft Hub, ' . $full_name . '.';
redirect_after_login($role);

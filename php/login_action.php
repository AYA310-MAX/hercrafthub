<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_to('login.php');
}

if (!rate_limit_allow('login', 8, 900)) {
    $_SESSION['error'] = 'Too many login attempts. Please wait 15 minutes and try again.';
    redirect_to('login.php');
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    $_SESSION['error'] = 'Please enter your email and password.';
    redirect_to('login.php');
}

$user = db_fetch_one(
    $conn,
    'SELECT id, full_name, password, role, is_active, profile_image FROM users WHERE email = ? LIMIT 1',
    's',
    [$email]
);

if ($user === null || !password_verify($password, (string) $user['password'])) {
    $_SESSION['error'] = 'Incorrect email or password. Please try again.';
    redirect_to('login.php');
}

if (!(int) $user['is_active']) {
    $_SESSION['error'] = 'Your account has been suspended. Please contact support.';
    redirect_to('login.php');
}

set_user_session((int) $user['id'], $user['full_name'], $user['role'], $user['profile_image'] ?? null);
redirect_after_login($user['role']);

<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../profile.php');
    exit;
}

$user_id   = (int) $_SESSION['user_id'];
$full_name = trim($_POST['full_name'] ?? '');
$location  = trim($_POST['location'] ?? '');
$bio       = trim($_POST['bio'] ?? '');

if ($full_name === '') {
    $_SESSION['error'] = 'Full name is required.';
    header('Location: ../profile.php');
    exit;
}

$existing = db_fetch_one(
    $conn,
    'SELECT profile_image FROM users WHERE id = ? LIMIT 1',
    'i',
    [$user_id]
);

if ($existing === null) {
    $_SESSION['error'] = 'Account not found.';
    header('Location: ../login.php');
    exit;
}

$profile_image = $existing['profile_image'];

if (!empty($_FILES['profile_image']['name'])) {
    $uploaded = save_profile_image($_FILES['profile_image']);
    if ($uploaded === null) {
        $_SESSION['error'] = 'Profile picture upload failed. Use JPG, PNG, or GIF under 2MB.';
        header('Location: ../profile.php');
        exit;
    }
    delete_profile_image($existing['profile_image']);
    $profile_image = $uploaded;
}

$updated = db_execute(
    $conn,
    'UPDATE users SET full_name = ?, location = ?, bio = ?, profile_image = ? WHERE id = ?',
    'ssssi',
    [$full_name, $location, $bio, $profile_image, $user_id]
);

if ($updated) {
    $_SESSION['full_name']     = $full_name;
    $_SESSION['profile_image'] = $profile_image;
    $_SESSION['success']       = 'Profile updated successfully.';
} else {
    $_SESSION['error'] = 'Unable to update profile. Please try again.';
}

header('Location: ../profile.php');
exit;

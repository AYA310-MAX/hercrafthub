<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.php'); exit;
}

$id   = (int)$_GET['id'];
$user = $conn->query("SELECT is_active FROM users WHERE id=$id")->fetch_assoc();

$new_status = $user['is_active'] ? 0 : 1;

$stmt = $conn->prepare("UPDATE users SET is_active=? WHERE id=?");
$stmt->bind_param("ii", $new_status, $id);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = $new_status ? "User activated." : "User suspended.";
header('Location: users.php');
exit;
?>
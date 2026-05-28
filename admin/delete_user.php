<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../login.php'); exit;
}

$id = (int)$_GET['id'];

// Prevent admin from deleting themselves
if ($id === $_SESSION['user_id']) {
  $_SESSION['error'] = "You cannot delete your own account.";
  header('Location: users.php');
  exit;
}

$stmt = $conn->prepare("DELETE FROM users WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

$_SESSION['success'] = "User deleted successfully.";
header('Location: users.php');
exit;
?>
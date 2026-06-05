<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['user_id'])) {
  header('Location: ../login.php'); exit;
}

$id        = $_SESSION['user_id'];
$full_name = trim($_POST['full_name']);
$location  = trim($_POST['location']);
$bio       = trim($_POST['bio']);

$stmt = $conn->prepare(
  "UPDATE users SET full_name=?, location=?, bio=? WHERE id=?"
);
$stmt->bind_param("sssi", $full_name, $location, $bio, $id);
$stmt->execute();
$stmt->close();

$_SESSION['full_name'] = $full_name;
$_SESSION['success']   = "Profile updated successfully.";
header('Location: ../profile.php');
exit;
?>
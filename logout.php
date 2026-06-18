<?php
session_start();
require_once 'includes/helpers.php';

$name = $_SESSION['full_name'] ?? 'Friend';
session_destroy();
session_start();
$_SESSION['goodbye'] = $name;
redirect_to('index.php');

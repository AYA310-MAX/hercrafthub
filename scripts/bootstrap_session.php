<?php
declare(strict_types=1);

/**
 * Localhost-only session bootstrap for screenshot automation.
 * Not for production use.
 */
$host = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($host, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Forbidden');
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$role = $_GET['role'] ?? 'admin';
$allowed = ['admin', 'seller', 'buyer'];

if (!in_array($role, $allowed, true)) {
    http_response_code(400);
    exit('Invalid role');
}

$user = db_fetch_one(
    $conn,
    'SELECT id, full_name, role, profile_image FROM users WHERE role = ? AND is_active = 1 ORDER BY id ASC LIMIT 1',
    's',
    [$role]
);

if ($user === null) {
    http_response_code(404);
    exit('No user found for role: ' . $role);
}

session_start();
set_user_session(
    (int) $user['id'],
    $user['full_name'],
    $user['role'],
    $user['profile_image'] ?? null
);

header('Content-Type: text/plain');
echo 'Session ready for ' . $user['role'] . ': ' . $user['full_name'];

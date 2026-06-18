<?php
declare(strict_types=1);

$allowed = ['users', 'categories', 'products', 'wishlists', 'sales', 'messages'];
$table = $_GET['table'] ?? '';

if (!in_array($table, $allowed, true)) {
    http_response_code(400);
    exit('Invalid table');
}

$host = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($host, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Local only');
}

require_once __DIR__ . '/../config/db.php';

header('Content-Type: text/html; charset=utf-8');

$safe = $conn->real_escape_string($table);
$result = $conn->query("SELECT * FROM `{$safe}` LIMIT 8");

if ($result === false) {
    echo '<p>Table not found.</p>';
    exit;
}

echo '<table><thead><tr>';
foreach ($result->fetch_fields() as $field) {
    echo '<th>' . htmlspecialchars($field->name) . '</th>';
}
echo '</tr></thead><tbody>';

$result->data_seek(0);
while ($row = $result->fetch_assoc()) {
    echo '<tr>';
    foreach ($row as $value) {
        $display = (string) $value;
        if (strlen($display) > 60) {
            $display = substr($display, 0, 57) . '...';
        }
        echo '<td>' . htmlspecialchars($display) . '</td>';
    }
    echo '</tr>';
}
echo '</tbody></table>';
$result->free();

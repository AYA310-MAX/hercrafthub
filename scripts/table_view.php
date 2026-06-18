<?php
declare(strict_types=1);

$allowed = ['users', 'categories', 'products', 'wishlists', 'sales', 'messages'];
$table = $_GET['table'] ?? 'users';

if (!in_array($table, $allowed, true)) {
    $table = 'users';
}

$host = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($host, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Local only');
}

require_once __DIR__ . '/../config/db.php';

$result = $conn->query('SELECT * FROM `' . $conn->real_escape_string($table) . '` LIMIT 8');
$columns = $result ? $result->fetch_fields() : [];
$rows = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
    $result->free();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>MySQL Table: <?= htmlspecialchars($table) ?></title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #faf7f2; padding: 32px; color: #2d1c42; margin: 0; }
    h1 { font-size: 1.5rem; margin: 0 0 8px; }
    .meta { color: #7a6b8a; margin-bottom: 20px; font-size: 0.9rem; }
    table { border-collapse: collapse; width: 100%; background: #fff; box-shadow: 0 4px 20px rgba(45,28,66,.1); }
    th, td { border: 1px solid #d4c5e2; padding: 10px 12px; text-align: left; font-size: 0.82rem; vertical-align: top; }
    th { background: #2d1c42; color: #f5f0e8; font-weight: 600; }
    tr:nth-child(even) { background: #f5f0e8; }
    .badge { display: inline-block; background: #ede5d8; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; }
  </style>
</head>
<body>
  <h1>MySQL Table: <span class="badge"><?= htmlspecialchars($table) ?></span></h1>
  <p class="meta">Database: <strong>hercrafthub</strong> &mdash; Sample rows from local XAMPP MySQL server</p>
  <?php if (count($rows) === 0): ?>
    <p>No rows found.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <?php foreach ($columns as $col): ?>
        <th><?= htmlspecialchars($col->name) ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $row): ?>
      <tr>
        <?php foreach ($row as $colName => $value): ?>
        <?php
          $display = (string) $value;
          if ($colName === 'password') {
              $display = '[bcrypt hash]';
          }
          if (strlen($display) > 80) {
              $display = substr($display, 0, 77) . '...';
          }
        ?>
        <td><?= htmlspecialchars($display) ?></td>
        <?php endforeach; ?>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</body>
</html>

<?php
declare(strict_types=1);

/**
 * One-time production installer. Visit once after uploading files, then delete this file.
 */
$lockFile = __DIR__ . '/.installed';

if (file_exists($lockFile)) {
    http_response_code(403);
    exit('HerCraft Hub is already installed. Delete install.php if it is still on the server.');
}

require_once __DIR__ . '/config/db.php';

$sqlFile = __DIR__ . '/database/schema_production.sql';
if (!is_readable($sqlFile)) {
    http_response_code(500);
    exit('Missing database/schema_production.sql');
}

$sql = file_get_contents($sqlFile);
$statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $sql)));

$errors = [];
foreach ($statements as $statement) {
    if ($statement === '' || str_starts_with($statement, '--')) {
        continue;
    }
    if ($conn->query($statement) === false) {
        $errors[] = $conn->error;
    }
}

if ($errors !== []) {
    http_response_code(500);
    echo '<h1>Install completed with warnings</h1><pre>' . htmlspecialchars(implode("\n", $errors)) . '</pre>';
    echo '<p>Some statements may have failed because tables already exist. Check your site.</p>';
} else {
    echo '<h1>HerCraft Hub installed successfully</h1>';
    echo '<p>Database tables and seed data are ready.</p>';
}

file_put_contents($lockFile, date('c'));
echo '<p><strong>Important:</strong> Delete <code>install.php</code> from the server now.</p>';
echo '<p><a href="index.php">Go to homepage</a></p>';

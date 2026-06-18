<?php
declare(strict_types=1);

/** Web path to the app root, e.g. "/hercrafthub" or "" when at document root. */
function app_base_path(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }

    $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $appRoot = realpath(dirname(__DIR__));

    if ($docRoot && $appRoot) {
        $docRoot = str_replace('\\', '/', $docRoot);
        $appRoot = str_replace('\\', '/', $appRoot);
        if (str_starts_with($appRoot, $docRoot)) {
            $base = rtrim(substr($appRoot, strlen($docRoot)), '/');
            return $base;
        }
    }

    $base = '';
    return $base;
}

/** Build a root-relative URL that works from any script depth. */
function url_for(string $path): string
{
    $path = ltrim(str_replace('\\', '/', $path), '/');
    $base = app_base_path();
    return ($base === '' ? '' : $base) . '/' . $path;
}

function redirect_to(string $path): void
{
    header('Location: ' . url_for($path));
    exit;
}

function product_image_src(?string $filename): string
{
    if ($filename !== null && $filename !== '') {
        $safe = basename($filename);
        $path = __DIR__ . '/../uploads/' . $safe;
        if (is_file($path)) {
            return 'uploads/' . $safe;
        }
    }
    return 'assets/images/default-product.svg';
}

function format_price(float|string $amount): string
{
    return 'R' . number_format((float) $amount, 2);
}

function flash_alert(string $type, string $message): string
{
    $class = $type === 'success' ? 'alert-success' : 'alert-danger';
    return '<div class="alert ' . $class . '">' . htmlspecialchars($message) . '</div>';
}

function render_flash_messages(): string
{
    $html = '';
    if (isset($_SESSION['success'])) {
        $html .= flash_alert('success', $_SESSION['success']);
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        $html .= flash_alert('error', $_SESSION['error']);
        unset($_SESSION['error']);
    }
    return $html;
}

function ensure_uploads_directory(): bool
{
    $dir = __DIR__ . '/../uploads';
    if (!is_dir($dir)) {
        return mkdir($dir, 0755, true);
    }
    return true;
}

function save_product_image(array $file): ?string
{
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return null;
    }

    if (!ensure_uploads_directory()) {
        return null;
    }

    $filename = uniqid('product_', true) . '.' . $ext;
    $destination = __DIR__ . '/../uploads/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }

    return $filename;
}

function delete_product_image(?string $filename): void
{
    if ($filename === null || $filename === '') {
        return;
    }
    $path = __DIR__ . '/../uploads/' . basename($filename);
    if (is_file($path)) {
        unlink($path);
    }
}

function get_categories(mysqli $conn): array
{
    return db_fetch_all($conn, 'SELECT id, name FROM categories ORDER BY name ASC');
}

function get_max_product_price(mysqli $conn): int
{
    $row = db_fetch_one(
        $conn,
        'SELECT COALESCE(MAX(price), 2000) AS max_price FROM products WHERE is_active = 1 AND is_sold = 0 AND quantity > 0'
    );
    $max = (int) ceil((float) ($row['max_price'] ?? 2000));
    return max($max, 500);
}

function profile_image_src(?string $filename): string
{
    if ($filename !== null && $filename !== '') {
        $safe = basename($filename);
        $path = __DIR__ . '/../uploads/avatars/' . $safe;
        if (is_file($path)) {
            return 'uploads/avatars/' . $safe;
        }
    }
    return '';
}

function ensure_avatars_directory(): bool
{
    $dir = __DIR__ . '/../uploads/avatars';
    if (!is_dir($dir)) {
        return mkdir($dir, 0755, true);
    }
    return true;
}

function save_profile_image(array $file): ?string
{
    if (empty($file['name']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        return null;
    }

    if (!ensure_avatars_directory()) {
        return null;
    }

    $filename = uniqid('avatar_', true) . '.' . $ext;
    $destination = __DIR__ . '/../uploads/avatars/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }

    return $filename;
}

function delete_profile_image(?string $filename): void
{
    if ($filename === null || $filename === '') {
        return;
    }
    $path = __DIR__ . '/../uploads/avatars/' . basename($filename);
    if (is_file($path)) {
        unlink($path);
    }
}

function render_user_avatar(string $name, ?string $profile_image = null, string $extra_class = ''): string
{
    $class = 'user-avatar' . ($extra_class !== '' ? ' ' . $extra_class : '');
    $initial = htmlspecialchars(strtoupper(substr($name, 0, 1)));
    $src = profile_image_src($profile_image);

    if ($src !== '') {
        return '<div class="' . $class . ' user-avatar-img">'
            . '<img src="' . htmlspecialchars($src) . '" alt="' . htmlspecialchars($name) . '">'
            . '</div>';
    }

    return '<div class="' . $class . '">' . $initial . '</div>';
}

/** Products visible in browse: active, not fully sold, stock remaining. */
function product_available_sql(): string
{
    return 'p.is_active = 1 AND p.is_sold = 0 AND p.quantity > 0';
}

function delivery_fee_amount(): float
{
    return 49.99;
}

function charity_donation_amount(): float
{
    return 5.00;
}

function sa_provinces(): array
{
    return [
        'Eastern Cape',
        'Free State',
        'Gauteng',
        'KwaZulu-Natal',
        'Limpopo',
        'Mpumalanga',
        'Northern Cape',
        'North West',
        'Western Cape',
    ];
}

function render_goodbye_modal(): string
{
    if (empty($_SESSION['goodbye'])) {
        return '';
    }
    $name = htmlspecialchars((string) $_SESSION['goodbye']);
    unset($_SESSION['goodbye']);
    return '<div class="modal fade" id="goodbyeModal" tabindex="-1" aria-hidden="true">'
        . '<div class="modal-dialog modal-dialog-centered"><div class="modal-content p-2">'
        . '<div class="modal-body text-center py-4">'
        . '<div class="user-avatar user-avatar-lg mx-auto mb-3">' . strtoupper(substr($name, 0, 1)) . '</div>'
        . '<h4 class="listing-title">Goodbye, ' . $name . '!</h4>'
        . '<p class="text-muted mb-4">Thanks for visiting HerCraft Hub. We hope to see you again soon.</p>'
        . '<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Take care</button>'
        . '</div></div></div></div>'
        . '<script>document.addEventListener("DOMContentLoaded",function(){'
        . 'new bootstrap.Modal(document.getElementById("goodbyeModal")).show();});</script>';
}

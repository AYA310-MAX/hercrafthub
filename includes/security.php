<?php
declare(strict_types=1);

const SESSION_TIMEOUT_SECONDS = 1800; // 30 minutes

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function touch_session(): void
{
    $_SESSION['last_activity'] = time();
}

function enforce_session_timeout(): void
{
    if (!isset($_SESSION['user_id'])) {
        return;
    }

    $last = (int) ($_SESSION['last_activity'] ?? 0);
    if ($last > 0 && (time() - $last) > SESSION_TIMEOUT_SECONDS) {
        session_destroy();
        session_start();
        $_SESSION['error'] = 'Your dashboard session expired after 30 minutes of inactivity. Please log in again.';
        redirect_to('login.php');
    }

    touch_session();
}

/**
 * Returns true if the action is allowed, false if rate-limited.
 */
function rate_limit_allow(string $action, int $max_attempts, int $window_seconds): bool
{
    $ip  = client_ip();
    $key = 'rate_' . $action;

    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = [];
    }

    $now = time();
    $_SESSION[$key] = array_values(array_filter(
        $_SESSION[$key],
        static fn (array $entry): bool => ($now - (int) $entry['time']) < $window_seconds
    ));

    $ip_attempts = 0;
    foreach ($_SESSION[$key] as $entry) {
        if (($entry['ip'] ?? '') === $ip) {
            $ip_attempts++;
        }
    }

    if ($ip_attempts >= $max_attempts) {
        return false;
    }

    $_SESSION[$key][] = ['ip' => $ip, 'time' => $now];
    return true;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

function assert_product_owner(mysqli $conn, int $product_id, int $user_id): ?array
{
    return db_fetch_one(
        $conn,
        'SELECT id, image, seller_id FROM products WHERE id = ? AND seller_id = ? LIMIT 1',
        'ii',
        [$product_id, $user_id]
    );
}

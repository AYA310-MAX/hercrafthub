<?php
declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/security.php';

function require_login(): void
{
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = 'Please log in to continue.';
        redirect_to('login.php');
    }
    enforce_session_timeout();
}

function require_buyer(): void
{
    require_login();
    if ($_SESSION['role'] === 'seller') {
        $_SESSION['error'] = 'Seller accounts cannot purchase items. Create a separate Buyer account with a different email to shop.';
        redirect_to('register.php');
    }
    if ($_SESSION['role'] === 'admin') {
        $_SESSION['error'] = 'Administrators cannot use the customer checkout.';
        redirect_to('dashboard.php');
    }
}

function require_role(array $roles): void
{
    require_login();
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $roles, true)) {
        $_SESSION['error'] = 'You do not have permission to access this page.';
        redirect_to('dashboard.php');
    }
}

function set_user_session(int $user_id, string $full_name, string $role, ?string $profile_image = null): void
{
    session_regenerate_id(true);
    $_SESSION['user_id']        = $user_id;
    $_SESSION['full_name']      = $full_name;
    $_SESSION['role']           = $role;
    $_SESSION['profile_image']  = $profile_image;
    touch_session();
}

function require_seller(): void
{
    require_login();
    if ($_SESSION['role'] !== 'seller') {
        $_SESSION['error'] = 'You need a Seller account to list items. Register a new account with the Seller role to start selling.';
        redirect_to('dashboard.php');
    }
}

function redirect_after_login(string $role): void
{
    if (!empty($_SESSION['redirect_after_login'])) {
        $target = $_SESSION['redirect_after_login'];
        unset($_SESSION['redirect_after_login']);
        if ($role !== 'admin' || strpos($target, 'admin') === false) {
            redirect_to($target);
        }
    }

    if ($role === 'admin') {
        redirect_to('admin/index.php');
    }

    redirect_to('dashboard.php');
}

<?php
$current = basename($_SERVER['PHP_SELF']);
if (!function_exists('profile_image_src')) {
    require_once __DIR__ . '/helpers.php';
}
$nav_avatar_src = isset($_SESSION['profile_image']) ? profile_image_src($_SESSION['profile_image']) : '';
?>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">

    <a class="navbar-brand" href="<?= htmlspecialchars(url_for('index.php')) ?>">
      HerCraft<span>Hub</span>
    </a>

    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#nav">
      <i class="ti ti-menu-2" style="font-size:1.4rem;color:var(--purple);"></i>
    </button>

    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= $current=='index.php'?'fw-bold':'' ?>"
             href="<?= htmlspecialchars(url_for('index.php')) ?>">Home</a>
        </li>
        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'seller'): ?>
        <li class="nav-item">
          <a class="nav-link <?= $current=='browse.php'?'fw-bold':'' ?>"
             href="<?= htmlspecialchars(url_for('browse.php')) ?>">Browse</a>
        </li>
        <?php endif; ?>
        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'buyer'): ?>
        <li class="nav-item">
          <a class="nav-link <?= $current=='sell.php'?'fw-bold':'' ?>"
             href="<?= htmlspecialchars(url_for('sell.php')) ?>">Sell</a>
        </li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav ms-auto align-items-center gap-2">

        <!-- Theme toggle -->
        <li class="nav-item d-flex align-items-center gap-2">
          <i class="ti ti-sun" style="font-size:1rem;color:var(--text-muted);"></i>
          <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode"></button>
          <i class="ti ti-moon" style="font-size:1rem;color:var(--text-muted);"></i>
        </li>

        <?php if(isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <a class="nav-link <?= $current=='dashboard.php'?'fw-bold':'' ?>"
               href="<?= htmlspecialchars(url_for('dashboard.php')) ?>">Dashboard</a>
          </li>
          <li class="nav-item">
            <a href="<?= htmlspecialchars(url_for('profile.php')) ?>" class="user-avatar<?= $nav_avatar_src !== '' ? ' user-avatar-img' : '' ?>"
               title="<?= htmlspecialchars($_SESSION['full_name']) ?>">
              <?php if ($nav_avatar_src !== ''): ?>
              <img src="<?= htmlspecialchars($nav_avatar_src) ?>" alt="<?= htmlspecialchars($_SESSION['full_name']) ?>">
              <?php else: ?>
              <?= strtoupper(substr($_SESSION['full_name'], 0, 1)) ?>
              <?php endif; ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger logout-trigger" href="#"
               data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= htmlspecialchars(url_for('login.php')) ?>">Login</a>
          </li>
          <li class="nav-item ms-2">
            <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars(url_for('register.php')) ?>">Join Free</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
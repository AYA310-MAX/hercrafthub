<?php $current = basename($_SERVER['PHP_SELF']); ?>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">

    <a class="navbar-brand" href="<?= strpos($current, 'admin') !== false ? '../index.php' : 'index.php' ?>">
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
             href="index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current=='browse.php'?'fw-bold':'' ?>"
             href="browse.php">Browse</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= $current=='sell.php'?'fw-bold':'' ?>"
             href="sell.php">Sell</a>
        </li>
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
               href="dashboard.php">Dashboard</a>
          </li>
          <!-- User avatar -->
          <li class="nav-item">
            <a href="profile.php" class="user-avatar"
               title="<?= htmlspecialchars($_SESSION['full_name']) ?>">
              <?= strtoupper(substr($_SESSION['full_name'], 0, 1)) ?>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link" href="login.php">Login</a>
          </li>
          <li class="nav-item ms-2">
            <a class="btn btn-primary btn-sm" href="register.php">Join Free</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
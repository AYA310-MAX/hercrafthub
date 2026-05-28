<?php $current = basename($_SERVER['PHP_SELF']); ?>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">✦ HerCraft<span>Hub</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link <?= $current=='index.php'?'fw-bold':'' ?>" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link <?= $current=='browse.php'?'fw-bold':'' ?>" href="browse.php">Browse</a></li>
        <li class="nav-item"><a class="nav-link <?= $current=='sell.php'?'fw-bold':'' ?>" href="sell.php">Sell</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="dashboard.php">My Dashboard</a></li>
          <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item ms-2">
            <a class="btn btn-primary btn-sm" href="register.php">Join Free</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
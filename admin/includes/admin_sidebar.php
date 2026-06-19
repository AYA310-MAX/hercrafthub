
<?php $page = basename($_SERVER['PHP_SELF']); ?>
<aside class="admin-sidebar">
  <div class="sidebar-brand-wrapper">
    <a href="index.php" class="admin-brand">✦ HerCraft<span>Hub</span> Admin</a>
  </div>
  <ul class="sidebar-menu">
    <li>
      <a href="index.php" class="<?= $page=='index.php'?'active':'' ?>">
        <i class="ti ti-layout-dashboard"></i> Dashboard
      </a>
    </li>
    <li>
      <a href="users.php" class="<?= $page=='users.php'?'active':'' ?>">
        <i class="ti ti-users"></i> Manage Users
      </a>
    </li>
    <li>
      <a href="listings.php" class="<?= $page=='listings.php'?'active':'' ?>">
        <i class="ti ti-package"></i> Manage Listings
      </a>
    </li>
    <li>
      <a href="sales.php" class="<?= $page=='sales.php'?'active':'' ?>">
        <i class="ti ti-coin"></i> Sales Tracking
      </a>
    </li>
    <li>
      <a href="../index.php" target="_blank">
        <i class="ti ti-world"></i> View Live Site
      </a>
    </li>
  </ul>
</aside>
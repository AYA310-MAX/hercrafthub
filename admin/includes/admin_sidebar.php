
<?php $page = basename($_SERVER['PHP_SELF']); ?>
<aside class="admin-sidebar">
  <ul class="sidebar-menu">
    <li>
      <a href="index.php" class="<?= $page=='index.php'?'active':'' ?>">
        📊 Dashboard
      </a>
    </li>
    <li>
      <a href="users.php" class="<?= $page=='users.php'?'active':'' ?>">
        👥 Manage Users
      </a>
    </li>
    <li>
      <a href="listings.php" class="<?= $page=='listings.php'?'active':'' ?>">
        📦 Manage Listings
      </a>
    </li>
    <li>
      <a href="../index.php" target="_blank">
        🌐 View Live Site
      </a>
    </li>
  </ul>
</aside>
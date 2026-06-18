<?php if (isset($_SESSION['user_id'])): ?>
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title listing-title" id="logoutModalLabel">Sign out?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-0">
          Are you sure you want to sign out, <strong><?= htmlspecialchars($_SESSION['full_name']) ?></strong>?
        </p>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Stay logged in</button>
        <a href="<?= htmlspecialchars(url_for('logout.php')) ?>" class="btn btn-danger" id="confirmLogoutBtn">
          <i class="ti ti-logout me-1"></i>Yes, sign out
        </a>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

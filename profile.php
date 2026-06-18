<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';
require_once 'includes/auth.php';

require_login();

$user_id = (int) $_SESSION['user_id'];

$user = db_fetch_one(
    $conn,
    'SELECT full_name, email, role, location, bio, profile_image, created_at FROM users WHERE id = ? LIMIT 1',
    'i',
    [$user_id]
);

if ($user === null) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$avatar_src = profile_image_src($user['profile_image']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Profile – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container my-5" style="max-width:680px;">

  <a href="dashboard.php" class="back-btn">
    <i class="ti ti-arrow-left"></i> Back to Dashboard
  </a>

  <?= render_flash_messages() ?>

  <div class="card p-4 p-md-5">

    <div class="text-center mb-4">
      <?php if ($avatar_src !== ''): ?>
      <div class="user-avatar user-avatar-lg user-avatar-img mx-auto mb-3">
        <img src="<?= htmlspecialchars($avatar_src) ?>" alt="<?= htmlspecialchars($user['full_name']) ?>">
      </div>
      <?php else: ?>
      <div class="user-avatar user-avatar-lg mx-auto mb-3">
        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
      </div>
      <?php endif; ?>
      <h2 class="listing-title"><?= htmlspecialchars($user['full_name']) ?></h2>
      <span class="role-badge role-<?= htmlspecialchars($user['role']) ?> mt-1 d-inline-block">
        <?= ucfirst(htmlspecialchars($user['role'])) ?>
      </span>
    </div>

    <form action="php/update_profile.php" method="POST" enctype="multipart/form-data">

      <div class="mb-3">
        <label class="form-label">Profile Picture</label>
        <input type="file" name="profile_image" id="profileImageInput" class="form-control" accept="image/*">
        <small class="text-muted">JPG, PNG or GIF. Maximum 2MB.</small>
        <div id="profileImagePreview" class="mt-3<?= $avatar_src === '' ? ' d-none' : '' ?>">
          <img id="profilePreviewImg"
               src="<?= $avatar_src !== '' ? htmlspecialchars($avatar_src) : '' ?>"
               alt="Profile preview" class="profile-preview-img">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="full_name" class="form-control"
               value="<?= htmlspecialchars($user['full_name']) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email Address</label>
        <input type="email" class="form-control"
               value="<?= htmlspecialchars($user['email']) ?>" disabled>
        <small class="text-muted">Email cannot be changed.</small>
      </div>

      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control"
               placeholder="e.g. Johannesburg, Gauteng"
               value="<?= htmlspecialchars((string) ($user['location'] ?? '')) ?>">
      </div>

      <div class="mb-4">
        <label class="form-label">Bio</label>
        <textarea name="bio" class="form-control" rows="3"
                  placeholder="Tell buyers a little about yourself..."><?= htmlspecialchars((string) ($user['bio'] ?? '')) ?></textarea>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2">
        Save Changes
      </button>

    </form>

    <hr class="my-4">

    <div class="text-center">
      <p class="text-muted small mb-0">
        Member since <?= date('F Y', strtotime($user['created_at'])) ?>
      </p>
      <a href="#" class="btn btn-outline-secondary btn-sm mt-3 logout-trigger"
         data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
    </div>

  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
<script>
document.getElementById('profileImageInput')?.addEventListener('change', function () {
  const file = this.files[0];
  const preview = document.getElementById('profileImagePreview');
  const img = document.getElementById('profilePreviewImg');
  if (!file || !preview || !img) return;
  const reader = new FileReader();
  reader.onload = function (e) {
    img.src = e.target.result;
    preview.classList.remove('d-none');
  };
  reader.readAsDataURL(file);
});
</script>
</body>
</html>

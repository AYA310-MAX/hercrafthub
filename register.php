<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Join HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4">
  <a href="javascript:history.back()" class="back-btn">
    <i class="ti ti-arrow-left"></i> Back
  </a>
</div>

<div class="auth-card">
  <div class="text-center mb-4">
    <h2>Join HerCraft Hub ✦</h2>
    <p class="text-muted">Create your free account and start selling today</p>
  </div>

  <!-- Success / error messages show here in Phase 3 -->
  <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>
  <?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <form action="php/register_action.php" method="POST" id="registerForm">

    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="full_name" class="form-control" 
             placeholder="e.g. Ayasha Mokoena" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Email Address</label>
      <input type="email" name="email" class="form-control" 
             placeholder="you@email.com" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Password</label>
      <div class="input-group">
        <input type="password" name="password" id="password" 
               class="form-control" placeholder="Min 6 characters" required>
        <button class="btn btn-outline-secondary" type="button" id="togglePass">👁</button>
      </div>
      <div class="mt-1">
        <div id="strength-bar" style="height:4px;border-radius:4px;width:0%;transition:width 0.3s,background 0.3s;"></div>
        <small id="strength-text" class="text-muted"></small>
      </div>
    </div>

    <div class="mb-3">
      <label class="form-label">Confirm Password</label>
      <input type="password" name="confirm_password" id="confirm_password" 
             class="form-control" placeholder="Repeat your password" required>
      <small id="match-msg" class="text-muted"></small>
    </div>

    <div class="mb-4">
      <label class="form-label">I want to</label>
      <select name="role" class="form-select" required>
        <option value="">-- Select your role --</option>
        <option value="buyer">Buy items (Buyer)</option>
        <option value="seller">Buy &amp; Sell items (Seller)</option>
      </select>
    </div>

    <div class="mb-3 form-check">
      <input type="checkbox" class="form-check-input" id="terms" required>
      <label class="form-check-label text-muted small" for="terms">
        I agree to the <a href="#" style="color:var(--purple);">Terms &amp; Conditions</a>
      </label>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2">Create My Account</button>

  </form>

  <p class="text-center text-muted mt-3 small">
    Already have an account? <a href="login.php" style="color:var(--purple);">Log in here</a>
  </p>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
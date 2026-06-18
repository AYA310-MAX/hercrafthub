<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
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
   <h2>Welcome Back</h2>
    <p class="text-muted">Log in to your HerCraft Hub account</p>
  </div>

  <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>
  <?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <form action="php/login_action.php" method="POST" id="loginForm">

    <div class="mb-3">
      <label class="form-label">Email Address</label>
      <input type="email" name="email" class="form-control" 
             placeholder="you@email.com" required autofocus>
    </div>

    <div class="mb-4">
      <label class="form-label">Password</label>
      <div class="input-group">
        <input type="password" name="password" id="password"
               class="form-control" placeholder="Your password" required>
        <button class="btn btn-toggle-pass" type="button" id="togglePass" aria-label="Toggle password visibility">
          <i class="ti ti-eye"></i>
        </button>
      </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2">Log In</button>

  </form>

  <p class="text-center text-muted mt-3 small">
    Don't have an account? 
    <a href="register.php" style="color:var(--purple);">Join free here</a>
  </p>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

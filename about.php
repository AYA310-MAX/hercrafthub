<?php
session_start();
require_once 'config/db.php';
require_once 'includes/helpers.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="">
<head>
  <link rel="icon" type="image/jpeg" href="images/logo.jpg">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>About Us – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    .about-section {
      padding: 60px 0;
    }
    .about-header {
      background: linear-gradient(135deg, var(--purple-light), var(--purple));
      color: white;
      padding: 100px 0;
      text-align: center;
    }
    .about-header h1 {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .about-header p {
      font-size: 1.25rem;
      opacity: 0.9;
      max-width: 700px;
      margin: 0 auto;
    }
    .mission-text {
      font-size: 1.15rem;
      line-height: 1.8;
      color: #444;
    }
    .core-value-card {
      background: #fff;
      border-radius: 12px;
      padding: 40px 30px;
      text-align: center;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      transition: transform 0.3s ease;
      height: 100%;
    }
    .core-value-card:hover {
      transform: translateY(-5px);
    }
    .core-value-icon {
      font-size: 3rem;
      color: var(--pink);
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="about-header">
  <div class="container">
    <h1>About HerCraft Hub</h1>
    <p>Empowering women artisans across South Africa by providing a premium platform to showcase and sell their creations.</p>
  </div>
</section>

<section class="about-section">
  <div class="container">
    <div class="row align-items-center mb-5">
      <div class="col-lg-6 mb-4 mb-lg-0">
        <h2 style="font-weight: 700; color: var(--purple); margin-bottom: 24px; font-size: 2.5rem;">Our Mission</h2>
        <p class="mission-text mb-4">
          HerCraft Hub was born out of a desire to create a dedicated space for women creators. We believe that handcrafted goods, tech crafts, and digital art deserve a marketplace that highlights the skill and passion of the women behind them.
        </p>
        <p class="mission-text">
          Our goal is to build a supportive community where sellers can thrive financially and buyers can discover unique, high quality products made with love right here in South Africa. We remove the barriers to entry so any woman can turn her craft into a business.
        </p>
      </div>
      <div class="col-lg-6">
        <div style="background: var(--cream); border-radius: 20px; padding: 40px; position: relative;">
          <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=600&h=400" alt="Women collaborating" style="width: 100%; border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
        </div>
      </div>
    </div>
  </div>
</section>

<section class="about-section" style="background: var(--cream-dark);">
  <div class="container">
    <div class="text-center mb-5">
      <h2 style="font-weight: 700; color: var(--purple);">Our Core Values</h2>
      <div class="section-divider mx-auto mt-3"></div>
    </div>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="core-value-card">
          <i class="ti ti-heart-handshake core-value-icon"></i>
          <h4>Community First</h4>
          <p class="text-muted mt-2">A platform built by women, for women. We prioritize connection and mutual support over transactions.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="core-value-card">
          <i class="ti ti-shield-check core-value-icon" style="color: var(--teal);"></i>
          <h4>Trust and Safety</h4>
          <p class="text-muted mt-2">We ensure a secure environment for both buyers and sellers, making every interaction safe and reliable.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="core-value-card">
          <i class="ti ti-bulb core-value-icon" style="color: var(--amber);"></i>
          <h4>Empowerment</h4>
          <p class="text-muted mt-2">Providing the tools, visibility, and platform needed for women to achieve financial independence.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

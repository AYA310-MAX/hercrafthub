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
  <title>Get In Touch – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    .contact-header {
      background: linear-gradient(135deg, var(--teal), #1a938c);
      color: white;
      padding: 80px 0;
      text-align: center;
    }
    .contact-header h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .contact-card {
      background: #fff;
      border-radius: 12px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      height: 100%;
    }
    .contact-icon {
      font-size: 2.5rem;
      color: var(--teal);
      margin-bottom: 20px;
      display: inline-block;
      background: rgba(43, 164, 157, 0.1);
      padding: 20px;
      border-radius: 50%;
    }
    .form-control, .form-select {
      padding: 12px 15px;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="contact-header">
  <div class="container">
    <h1>Help and Customer Service</h1>
    <p style="font-size: 1.2rem; opacity: 0.9; max-width: 600px; margin: 0 auto;">
      We are here to support you. Whether you are a buyer, seller, or employee, our service team is ready to assist.
    </p>
  </div>
</section>

<section class="container my-5">
  <div class="row g-5">
    
    <div class="col-lg-5">
      <div class="contact-card">
        <h3 style="font-weight: 700; color: var(--purple); margin-bottom: 30px;">Contact Information</h3>
        
        <div class="d-flex mb-4 align-items-start">
          <i class="ti ti-clock contact-icon" style="padding: 12px; font-size: 1.8rem; margin-right: 20px; margin-bottom: 0;"></i>
          <div>
            <h5 style="font-weight: 600;">Response Time</h5>
            <p class="text-muted mb-0">Please note that our support team reviews all inquiries carefully. You will receive help after 24 hours.</p>
          </div>
        </div>

        <div class="d-flex mb-4 align-items-start">
          <i class="ti ti-mail contact-icon" style="padding: 12px; font-size: 1.8rem; margin-right: 20px; margin-bottom: 0;"></i>
          <div>
            <h5 style="font-weight: 600;">Business Email</h5>
            <p class="text-muted mb-0">support@hercrafthub.co.za</p>
          </div>
        </div>

        <div class="d-flex mb-4 align-items-start">
          <i class="ti ti-map-pin contact-icon" style="padding: 12px; font-size: 1.8rem; margin-right: 20px; margin-bottom: 0;"></i>
          <div>
            <h5 style="font-weight: 600;">Headquarters</h5>
            <p class="text-muted mb-0">12 Innovation Way<br>Johannesburg, 2000<br>South Africa</p>
          </div>
        </div>
        
        <div class="d-flex mb-4 align-items-start">
          <i class="ti ti-users contact-icon" style="padding: 12px; font-size: 1.8rem; margin-right: 20px; margin-bottom: 0;"></i>
          <div>
            <h5 style="font-weight: 600;">Employee Service</h5>
            <p class="text-muted mb-0">Internal team members can also use the form to reach Human Resources for confidential support.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-7">
      <div class="contact-card">
        <h3 style="font-weight: 700; color: var(--purple); margin-bottom: 30px;">Send Us A Message</h3>
        <form action="#" method="POST">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label">Full Name</label>
              <input type="text" class="form-control" placeholder="Your name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control" placeholder="name@example.com" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Inquiry Type</label>
            <select class="form-select" required>
              <option value="" disabled selected>Select an option</option>
              <option value="customer">Customer Support</option>
              <option value="seller">Seller Assistance</option>
              <option value="employee">Employee Service</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="form-label">Message</label>
            <textarea class="form-control" rows="6" placeholder="How can we help you today?" required></textarea>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-3" style="font-size: 1.1rem; font-weight: 600;">Submit Request</button>
        </form>
      </div>
    </div>

  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>

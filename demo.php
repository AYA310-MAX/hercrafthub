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
  <title>App Demo & FAQ – HerCraft Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <style>
    .demo-header {
      background: linear-gradient(135deg, var(--purple-light), var(--purple));
      color: white;
      padding: 80px 0;
      text-align: center;
    }
    .demo-header h1 {
      font-size: 3rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    .step-card {
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.05);
      position: relative;
      height: 100%;
    }
    .step-number {
      background: var(--pink);
      color: white;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 1.2rem;
      margin-bottom: 20px;
    }
    .faq-card {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.03);
      margin-bottom: 15px;
      border: 1px solid rgba(0,0,0,0.05);
    }
    .accordion-button:not(.collapsed) {
      background-color: var(--purple-light);
      color: white;
    }
    .accordion-button:not(.collapsed)::after {
      filter: brightness(0) invert(1);
    }
  </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<section class="demo-header">
  <div class="container">
    <h1>How It Works</h1>
    <p style="font-size: 1.2rem; opacity: 0.9; max-width: 700px; margin: 0 auto;">
      Welcome to the interactive guide. See exactly how buyers and sellers use HerCraft Hub to connect and trade safely.
    </p>
  </div>
</section>

<section class="container my-5">
  <div class="text-center mb-5">
    <h2 style="font-weight: 700; color: var(--purple);">For Buyers</h2>
    <div class="section-divider mx-auto mt-3"></div>
    <p class="text-muted mt-3">Discover unique items and purchase securely in just three easy steps.</p>
  </div>

  <div class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="step-card">
        <div class="step-number">1</div>
        <h4>Browse Categories</h4>
        <p class="text-muted mt-3">Navigate through our distinct categories or use the search bar to find handmade goods, digital art, or tech crafts that inspire you.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="step-card">
        <div class="step-number">2</div>
        <h4>Review Listings</h4>
        <p class="text-muted mt-3">Click on any item to read the detailed description, see high quality images, and verify the seller information and location.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="step-card">
        <div class="step-number">3</div>
        <h4>Secure Checkout</h4>
        <p class="text-muted mt-3">Select your preferred mock payment method and place your order safely. Your funds are protected until delivery is confirmed.</p>
      </div>
    </div>
  </div>

  <div class="text-center mb-5 mt-5">
    <h2 style="font-weight: 700; color: var(--purple);">For Sellers</h2>
    <div class="section-divider mx-auto mt-3"></div>
    <p class="text-muted mt-3">Set up your shop and start earning from your crafts seamlessly.</p>
  </div>

  <div class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="step-card">
        <div class="step-number" style="background: var(--teal);">1</div>
        <h4>Create Your Profile</h4>
        <p class="text-muted mt-3">Register as a seller and verify your account. Add a profile picture and bio to build trust with potential buyers.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="step-card">
        <div class="step-number" style="background: var(--teal);">2</div>
        <h4>List Products</h4>
        <p class="text-muted mt-3">Upload clear photos, write compelling descriptions, set your price, and publish. Your listing goes live instantly.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="step-card">
        <div class="step-number" style="background: var(--teal);">3</div>
        <h4>Manage Sales</h4>
        <p class="text-muted mt-3">Use your dashboard to track inventory, communicate with buyers, and process orders. Get paid directly to your account.</p>
      </div>
    </div>
  </div>
</section>

<section id="faq" style="background: var(--cream-dark); padding: 80px 0;">
  <div class="container">
    <div class="text-center mb-5">
      <h2 style="font-weight: 700; color: var(--purple);">Frequently Asked Questions</h2>
      <div class="section-divider mx-auto mt-3"></div>
      <p class="text-muted mt-3">Find comprehensive answers to common inquiries below.</p>
    </div>

    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="accordion" id="faqAccordion">
          
          <div class="accordion-item faq-card">
            <h2 class="accordion-header" id="headingOne">
              <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                How do I know the sellers are legitimate?
              </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-muted">
                Every seller on HerCraft Hub goes through a strict verification process. We verify their email addresses and monitor their activity closely. We also provide a rating system allowing buyers to review their experiences, ensuring high quality standards across the platform.
              </div>
            </div>
          </div>

          <div class="accordion-item faq-card">
            <h2 class="accordion-header" id="headingTwo">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                Are my payments secure?
              </button>
            </h2>
            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-muted">
                Absolutely. We use industry standard encryption to process all transactions. Our platform integrates with trusted payment gateways, ensuring that your financial information is never stored directly on our servers and is fully protected.
              </div>
            </div>
          </div>

          <div class="accordion-item faq-card">
            <h2 class="accordion-header" id="headingThree">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                Can I sell both physical and digital products?
              </button>
            </h2>
            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-muted">
                Yes, our marketplace accommodates both. You can list physical handmade goods requiring delivery, or digital templates and art that buyers can download immediately upon purchase.
              </div>
            </div>
          </div>

          <div class="accordion-item faq-card">
            <h2 class="accordion-header" id="headingFour">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                What happens if I encounter an issue with my order?
              </button>
            </h2>
            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
              <div class="accordion-body text-muted">
                We have a dedicated support team ready to assist. You can reach out to us via our Get In Touch page. As per our policy, we aim to review and provide help after 24 hours. Buyers are fully protected under our dispute resolution framework.
              </div>
            </div>
          </div>

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

<?php
require_once('./layout/header.php');
?>

<!-- Marquee section -->
<div class="marquee" data-text="💬 Have questions? We’re here to help! Reach out below 💬"></div>

<!-- Enquire section -->
<section class="enquire-section">
    <h3 class="enquire-heading">Send an Enquiry</h3>
    <p>We aim to respond within 1–3 business days. For urgent matters,
        please call the office.</p>

    <div class="enquire-container">

        <!-- LEFT IMAGE -->
        <div class="enquire-image_container">
            <img src="<?= BASE_URL ?>/assets/images/customer-img.jpg" alt="enquire-img" class="enquire-img">
        </div>

        <!-- FORM -->
        <form id="enquiryForm" method="POST" class="enquire-form_container">
            <p class="error-mssg">All fields are required</p>

            <div class="input_wrap">
                <label for="fullname">Full Name</label>
                <input class="fullname" type="text" name="fullname" id="fullname">
            </div>

            <div class="input_wrap">
                <label for="email">Email</label>
                <input class="enquire-email" type="email" name="email" id="email">
            </div>

            <div class="input_wrap">
                <label for="number">Number</label>
                <input class="enquire-number" type="tel" name="phone" id="number">
            </div>

            <div class="input_wrap">
                <label for="message">Message</label>
                <textarea class="enquire-textarea" name="message" id="message"
                    rows="8" placeholder="Type your message here..."></textarea>
            </div>

            <!-- BUTTON OUTSIDE INPUT WRAPPER -->
            <button type="submit" class="enquire-btn">Send</button>

        </form>

    </div>
</section>

<?php
require_once('./layout/footer.php');
?>
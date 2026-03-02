<?php
require_once('./layout/header.php');
?>

<!-- Marquee section -->
<div class="marquee" data-text="📞 Stay Connected: Follow us on social media for latest updates! 📞"></div>

<!-- Contact section -->
<section class="contact-section">
    <h3 class="contact-heading">Connect With Us</h3>
    <p>We’re here to answer your questions and guide you every step of the way</p>

    <div class="contact-container">

        <!-- Email -->
        <div class="conatact-wrap">
            <img src="<?= BASE_URL ?>/assets/icon/mark_email_unread.png" alt="email-img" class="email-img">
            <a href="mailto:support@veefashion.com">support@veefashion.com</a>
        </div>

        <!-- Phone -->
        <div class="conatact-wrap">
            <img src="<?= BASE_URL ?>/assets/icon/call_log.png" alt="phone-img" class="phone-img">
            <a href="tel:+234-812-345-6789">+234-812-345-6789</a>
        </div>

    </div>

</section>

<?php
require_once('./layout/footer.php');
?>
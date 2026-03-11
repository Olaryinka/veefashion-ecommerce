<!-- Footer section -->
<?php
ob_end_flush();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");
require_once("$root_folder/config/site_setting.php");
?>
<footer>
    <div class="footer-container">

        <div class="legal-container">
            <h2>Legal</h2>
            <ul class="legal-list">
                <li><a href="#">Privacy</a></li>
                <li><a href="#">Terms & Condition​</a></li>
                <li><a href="#">Refund Policy</a></li>
            </ul>
        </div>

        <div class="hour-container">
            <h2>Operating Hours</h2>
            <ul class="hour-list">
                <li>Mon - Fri: 8am - 8pm</li>
                <li>Saturday: 9am - 7pm​</li>
                <li>Sunday: 9am - 8pm</li>
            </ul>

            <div class="social-links">
                <h3>Follow us for fashion tips</h3>
                <div class="links-img">
                    <a href="https://www.instagram.com/" target="_blank">
                        <img src="<?= BASE_URL ?>/assets/icon/instagram-img.png" class="insta_social-img" alt="instagram">
                    </a>
                    <a href="https://www.facebook.com/" target="_blank">
                        <img src="<?= BASE_URL ?>/assets/icon/facebook-img.png" class="social-img" alt="facebook">
                    </a>
                </div>
            </div>
        </div>

        <div class="contact-container">
            <h2>Contact</h2>
            <ul class="contact-list">
                <li>The Vee-Fashion House, 1059 O.P. Fingesi Road, Mabushi, Abuja 900108, FCT</li>
                <li><a href="tel:<?= preg_replace('/\s+/', '', $CONTACT_PHONE) ?>"><?= $CONTACT_PHONE ?></a></li>
                <li><a href="mailto:<?= esc($CONTACT_EMAIL) ?>"><?= $CONTACT_EMAIL ?></a></li>
            </ul>
        </div>

    </div>

    <p class="copyright">
        &copy; Vee - Fashion House • <span class="year"><?= date("Y") ?></span>
    </p>
</footer>

</body>


</html>

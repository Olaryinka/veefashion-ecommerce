<?php
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once ("$root_folder/config/db.php");
require_once("$root_folder/layout/header.php");
?>

<div class="signup_signin-container">
    <div class="singup_image">
        <img src="<?= BASE_URL ?>/assets/images/signup_img.jpeg" alt="signup_img" class="signup_img">
    </div>
    <div class="form_container">
        <h3>Sign Up</h3>
        <p>Sign up for exclusive fashion perks</p>
        <form id="signupForm" enctype="multipart/form-data" action="" method="POST" class="signup_form">
            <input type="hidden" name="trigger_signup">
            <input type="file" name="image" class="imgUpload" accept=".jpeg, .jpg, .png">
            <input type="text" name="firstname" placeholder="First Name">
            <input type="text" name="lastname" placeholder="Last Name">
            <input type="tel" name="phone" placeholder="Phone Number">
            <input type="email" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Password">
            <button type="submit" class="signup_btn">Sign Up</button>
            <a href="<?= BASE_URL ?>/signin.php" class="login_account">Already have an account? <span>Login</span></a>
        </form>
    </div>
</div>
<?php
require_once("$root_folder/layout/footer.php");
?>


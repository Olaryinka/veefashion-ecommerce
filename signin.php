<?php
require_once('./layout/header.php');
?>

<div class="signup_signin-container">
    <div class="singin_image">
        <img src="<?= BASE_URL ?>/assets/images/signin_img.png" alt="signin_img" class="signin_img">
    </div>
    <div class="form_container signin-container">
        <h3>Sign In</h3>
        <p>Welcome back to your world of style</p>
        <form id="signinForm" action="" method="POST" class="signin_form">
            <input type="hidden" name="trigger_signin">
            <input type="text" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Password">
            <button id="signin_btn" type="submit" class="signup_btn">Login</button>
            <a href="<?= BASE_URL ?>/signup.php" class="login_account">Don't have an account? <span>Sign Up</span></a>
        </form>
    </div>
</div>
<?php
require_once('./layout/footer.php');
?>

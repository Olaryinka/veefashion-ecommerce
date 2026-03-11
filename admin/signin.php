<?php
require_once('../layout/header.php');

if(isset($_SESSION['admin_id'])){
    header("Location: /veefashion/admin/dashboard/admin.php");
    exit;
};
?>

<div class="signup_signin-container">
    <div class="singin_image">
        <img src="<?= BASE_URL ?>/assets/images/signin_img.png" alt="signin_img" class="signin_img">
    </div>
    <div class="form_container signin-container">
        <h3>Sign In</h3>
        <p>Access your admin panel securely.</p>
        <form id="admin_SigninForm"  action="" method="POST" class="signin_form">
            <input type="hidden" name="trigger_admin_signup">
            <input type="text" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Password">
            <button type="submit" class="signup_btn">Login</button>
        </form>
    </div>
</div>
<?php
require_once('../layout/footer.php');
?>


<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
define('BASE_URL', '/veefashion');
require_once("$root_folder/config/env.php");
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");
require_once("$root_folder/config/site_setting.php");

$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += $item['qty'];
    }
}
$userAvatar = BASE_URL . "/assets/images/avatar.jpg";
// If user is logged in, try to use profile image
if (isset($_SESSION['id'])) {
    // Option 1: from session (FAST & recommended)
    $sessionImage = $_SESSION['image'] ?? '';
    if (
        !empty($sessionImage) &&
        file_exists($_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/" . $sessionImage)
    ) {
        $userAvatar = BASE_URL . "/assets/images/profile_upload/" . $sessionImage;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $SITE_NAME ?></title>

    <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/icon/favicon.png" type="image/x-icon">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/custom/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/custom/responsive.css">

    <!-- Scripts -->
    <script defer src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script defer src="<?= BASE_URL ?>/assets/script/jquery.min.js"></script>
    <script defer src="<?= BASE_URL ?>/assets/script/sweetalert.min.js"></script>
    <!-- Inject Paystack key BEFORE script.js -->
    <script>
        window.PAYSTACK_PUBLIC_KEY = "<?= $_ENV['PAYSTACK_PUBLIC_KEY'] ?>";
    </script>

    <script defer src="<?= BASE_URL ?>/assets/script/script.js"></script>
    <script defer src="https://js.paystack.co/v1/inline.js"></script>
</head>

<body id="site">

    <!-- HEADER SECTION -->
    <header>
        <div class="header-image_container">
            <a href="<?= BASE_URL ?>/index.php">
                <img src="<?= $SITE_LOGO ?>" alt="header-logo" class="header-logo">
            </a>
        </div>

        <ul class="nav-links">
            <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>
            <li><a href="<?= BASE_URL ?>/about.php">About</a></li>
            <li><a href="<?= BASE_URL ?>/gallery.php">Collection</a></li>
            <li><a href="<?= BASE_URL ?>/event.php">Events</a></li>
            <li><a href="<?= BASE_URL ?>/enquire.php">Enquires</a></li>
            <?php if (!isset($_SESSION['id'])): ?>
                <li><a href="<?= BASE_URL ?>/signin.php">Login</a></li>
            <?php endif; ?>
        </ul>
        <!-- User avatar dropdown -->
        <?php if (isset($_SESSION['id'])): ?>
            <div class="user-menu">
                <img src="<?= $userAvatar ?>" class="nav-avatar" id="userMenuToggle">
                <div class="user-dropdown">
                    <a href="<?= BASE_URL ?>/user/dashboard.php">Dashboard</a>
                    <a href="<?= BASE_URL ?>/user/orders.php">My Orders</a>
                    <a href="<?= BASE_URL ?>/user/user_settings.php">Settings</a>
                    <a href="<?= BASE_URL ?>/user/logout.php" class="logout">Logout</a>
                </div>
            </div>
        <?php endif; ?>

        <!-- CART -->
        <div class="cart">
            <a href="<?= BASE_URL ?>/cart.php">
                <span class="count"><?= $cartCount ?></span>
                <i class="fa-solid fa-cart-plus"></i>
            </a>
        </div>

        <!-- MOBILE MENU -->
        <div class="menubar">
            <img src="<?= BASE_URL ?>/assets/icon/menu.png" alt="menu-logo" class="menu-logo">
        </div>
    </header>

    <!-- MAIN STARTS HERE -->
    <main>
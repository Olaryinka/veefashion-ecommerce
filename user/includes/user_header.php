<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
define('BASE_URL', '/veefashion');
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");
require_once("$root_folder/config/site_setting.php");

//AUTH CHECK
// 1. Must be logged in
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'User') {
    header("Location: /veefashion/signin.php");
    exit;
}

// 2. Re-check user status from DB
$stmt = $conn->prepare("
    SELECT status, is_deleted
    FROM users
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// 3. User no longer exists or deleted
if (!$user || $user['is_deleted'] == 1) {
    session_destroy();
    header("Location: /veefashion/signin.php");
    exit;
}

// 4. Suspended user
if ($user['status'] !== 'Active') {
    session_destroy();
    header("Location: /veefashion/signin.php?error=suspended");
    exit;
}


$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/";
if (!empty($_SESSION['image']) && file_exists($uploadDir . $_SESSION['image'])) {
    $userImage = BASE_URL . "/assets/images/profile_upload/" . $_SESSION['image'];
} else {
    $userImage = BASE_URL . "/assets/images/avatar.jpg";
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/custom/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/custom/responsive.css">
    <script defer src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script defer src="<?= BASE_URL ?>/assets/script/jquery.min.js"></script>
    <script defer src="<?= BASE_URL ?>/assets/script/sweetalert.min.js"></script>
    <script defer src="<?= BASE_URL ?>/assets/script/script.js"></script>
    <script defer src="https://js.paystack.co/v1/inline.js"></script>
</head>

<body>
    <div class="user_dashboard">
        <!-- SIDEBAR -->
        <aside class="dash_sidebar">
            <div class="logo_section">
                <a href="<?= BASE_URL ?>" class="homepage-link">
                    <img src="<?= $SITE_LOGO ?>" class="dash-logo">
                    <h2 class="dash_logo_title">VeeFashion</h2>
                </a>
                <div class="profile_area">
                    <img src="<?= $userImage ?>" class="user_avatar">
                    <div class="user_notification">
                        <i class="fa-solid fa-bell notifications"></i>
                        <i class="fa-solid fa-envelope notifications"></i>
                    </div>
                </div>
            </div>

            <ul class="dash_menu">
                <li><a href="<?= BASE_URL ?>/user/dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="<?= BASE_URL ?>/user/orders.php"><i class="fa-solid fa-box"></i> Orders</a></li>
                <li><a href="<?= BASE_URL ?>/user/user_settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
            </ul>

            <a href="<?= BASE_URL ?>/user/logout.php" class="dash_logout_btn">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>

        </aside>


        <!-- MAIN AREA -->
        <main class="dash_main">
            <div class="dash_topbar">
                <h3>Welcome Back, <?= isset($_SESSION['fullname']) ? esc($_SESSION['fullname']) : 'User' ?> 👋 </h3>
                <div class="hamburger" id="hamburger">
                    <i class="fa-solid fa-bars"></i>
                </div>
            </div>
            <P>Here you can manage your account information and track your orders.</P>
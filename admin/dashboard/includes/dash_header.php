<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
define('BASE_URL', '/veefashion');
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");
require_once("$root_folder/config/mail.php");
require_once("$root_folder/config/site_setting.php");

//    ADMIN AUTH CHECK (LOGIN ONLY)
if (
    !isset($_SESSION['admin_id']) ||
    $_SESSION['role'] !== 'Admin'
) {
    header("Location: /veefashion/admin/signin.php");
    exit;
}

/* Re-check admin status */
$stmt = $conn->prepare("
    SELECT status, is_deleted, role
    FROM admins
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $_SESSION['admin_id']);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

if (!$admin || $admin['is_deleted'] == 1) {
    session_destroy();
    header("Location: /veefashion/admin/signin.php");
    exit;
}

if ($admin['status'] !== 'Active') {
    session_destroy();
    header("Location: /veefashion/admin/signin.php?error=suspended");
    exit;
}
/* Keep role fresh */
$_SESSION['admin_role'] = $admin['role'];

$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("
    SELECT image, fullname
    FROM admins
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/";
if (!empty($admin['image']) && file_exists($uploadDir . $admin['image'])) {
    $adminImage = BASE_URL . "/assets/images/profile_upload/" . $admin['image'];
} else {
    $adminImage = BASE_URL . "/assets/images/avatar-image.png";
}

$logoFile = $settings['logo'] ?? '';
$logoPath = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/icon/" . $logoFile;
if (!empty($logoFile) && file_exists($logoPath)) {
    $logoUrl = BASE_URL . "/assets/icon/" . $logoFile;
} else {
    $logoUrl = BASE_URL . "/assets/icon/logo.png"; // fallback
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
    <script defer src="<?= BASE_URL ?>/assets/script/script.js"></script>
    <script defer src="<?= BASE_URL ?>/assets/script/sweetalert.min.js"></script>
</head>

<body>
    <div class="admin_dashboard">
        <!-- SIDEBAR -->
        <aside class="dash_sidebar">
            <div class="logo_section">
                <a href="<?= BASE_URL ?>" class="homepage-link">
                    <img src="<?= $SITE_LOGO ?>" class="dash-logo">
                    <h2 class="dash_logo_title">VeeFashion</h2>
                </a>
            </div>

            <ul class="dash_menu">
                <li><a href="<?= BASE_URL ?>/admin/dashboard/admin.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
                <li><a href="<?= BASE_URL ?>/admin/dashboard/products.php"><i class="fa-solid fa-shirt"></i> Products</a></li>
                <li><a href="<?= BASE_URL ?>/admin/dashboard/orders.php"><i class="fa-solid fa-box"></i> Orders</a></li>
                <li><a href="<?= BASE_URL ?>/admin/dashboard/order_items.php"><i class="fa-solid fa-cart-flatbed"></i> Order Items</a></li>
                <li><a href="<?= BASE_URL ?>/admin/dashboard/admins.php"><i class="fa-solid fa-user-shield"></i> Admins</a></li>
                <li><a href="<?= BASE_URL ?>/admin/dashboard/users.php"><i class="fa-solid fa-users"></i> Users</a></li>
                <li><a href="<?= BASE_URL ?>/admin/dashboard/enquiries.php"><i class="fa-solid fa-envelope"></i> Enquiries</a></li>
                <li><a href="<?= BASE_URL ?>/admin/dashboard/admin_settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
            </ul>

            <a href="<?= BASE_URL ?>/admin/logout.php" class="dash_logout_btn">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>

        </aside>


        <!-- MAIN AREA -->
        <main class="dash_main">
            <div class="dash_topbar">
                <h3>Welcome Back, <?= isset($_SESSION['fullname']) ? esc($_SESSION['fullname']) : 'Admin' ?>
                </h3>

                <div class="profile_area">
                    <i class="fa-solid fa-bell notifications"></i>
                    <a href="<?= BASE_URL ?>/admin/dashboard/admin_profile.php" class="edit_btn">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <img src="<?= $adminImage ?>" class="admin_avatar">
                </div>
            </div>
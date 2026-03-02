<?php
require_once(__DIR__ . "/includes/dash_header.php");

// DASHBOARD STATISTICS

// PRODUCTS
$productStats = $conn->query("
    SELECT 
        COUNT(*) AS total,
        SUM(CASE WHEN quantity > 0 THEN 1 ELSE 0 END) AS available,
        SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) AS out_of_stock
    FROM products
    WHERE is_deleted = 0
")->fetch_assoc();

// SALES
$salesStats = $conn->query("
    SELECT COALESCE(SUM(total_amount), 0) AS total_sales
    FROM orders
    WHERE status = 'Paid'
")->fetch_assoc();

// ORDERS
$orderStats = $conn->query("
    SELECT 
        COUNT(*) AS total_orders,
        SUM(CASE WHEN status = 'Delivered' THEN 1 ELSE 0 END) AS delivered,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled
    FROM orders
")->fetch_assoc();

// ADMINS
$adminStats = $conn->query("
    SELECT COUNT(*) AS total_admins
    FROM admins
    WHERE is_deleted = 0
")->fetch_assoc();

// USERS
$userStats = $conn->query("
    SELECT COUNT(*) AS total_customers
    FROM users
    WHERE is_deleted = 0
")->fetch_assoc();


?>
<!-- SUMMARY BOXES -->
<section class="dash_cards">

    <div class="dash_card card_green">
        <div class="card_icon"><i class="fa-solid fa-shirt"></i></div>
        <h4>Total Products</h4>
        <p class="value"><?= $productStats['total'] ?></p>
        <span> <?= $productStats['available'] ?> Available ·
            <?= $productStats['out_of_stock'] ?> Out-of-stock
        </span>
    </div>

    <div class="dash_card card_pink">
        <div class="card_icon"><i class="fa-solid fa-money-bill"></i></div>
        <h4>Total Sales</h4>
        <p class="value">₦<?= number_format($salesStats['total_sales']) ?></p>
        <span>All completed payments</span>
    </div>

    <div class="dash_card card_dark">
        <div class="card_icon"><i class="fa-solid fa-box"></i></div>
        <h4>Total Orders</h4>
        <p class="value"><?= $orderStats['total_orders'] ?></p>
        <span><?= $orderStats['delivered'] ?> Delivered -
            <?= $orderStats['pending'] ?> Pending -
            <?= $orderStats['cancelled'] ?> Cancelled
        </span>
    </div>

    <div class="dash_card card_blue">
        <div class="card_icon"><i class="fa-solid fa-users"></i></div>
        <h4>Total Users</h4>
        <p class="value"><?= $adminStats['total_admins'] + $userStats['total_customers'] ?></p>
        <span><?= $adminStats['total_admins'] ?> Admins ·
            <?= $userStats['total_customers'] ?> Customers
        </span>
    </div>

</section>
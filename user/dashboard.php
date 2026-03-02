<?php
require_once("./includes/user_header.php");


// DASHBOARD STATISTICS
$user_id = $_SESSION['id'];

// Order Stats
$orderStats = $conn->prepare("
    SELECT
        COUNT(*) AS total_orders,
        SUM(CASE WHEN status = 'Delivered' THEN 1 ELSE 0 END) AS delivered,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled
    FROM orders
    WHERE user_id = ?
");
$orderStats->bind_param("i", $user_id);
$orderStats->execute();
$orderStats = $orderStats->get_result()->fetch_assoc();

// Payments Stats
$paymentStats = $conn->prepare("
    SELECT COALESCE(SUM(total_amount), 0) AS total_spent
    FROM orders
    WHERE user_id = ? AND status = 'Paid'
");
$paymentStats->bind_param("i", $user_id);
$paymentStats->execute();
$paymentStats = $paymentStats->get_result()->fetch_assoc();


?>
<!-- SUMMARY BOXES -->
<section class="dash_cards">


    <!-- TOTAL ORDERS -->
    <div class="dash_card card_dark">
        <div class="card_icon"><i class="fa-solid fa-box"></i></div>
        <h4>My Orders</h4>
        <p class="value"><?= $orderStats['total_orders'] ?></p>
        <span>
            <?= $orderStats['delivered'] ?> Delivered ·
            <?= $orderStats['pending'] ?> Pending ·
            <?= $orderStats['cancelled'] ?> Cancelled
        </span>
    </div>

    <!-- DELIVERED ORDERS -->
    <div class="dash_card card_green">
        <div class="card_icon"><i class="fa-solid fa-truck"></i></div>
        <h4>Delivered Orders</h4>
        <p class="value"><?= $orderStats['delivered'] ?></p>
        <span>Successfully completed orders</span>
    </div>

    <!-- TOTAL SPENT -->
    <div class="dash_card card_pink">
        <div class="card_icon"><i class="fa-solid fa-money-bill-wave"></i></div>
        <h4>Total Spent</h4>
        <p class="value">₦<?= number_format($paymentStats['total_spent']) ?></p>
        <span>Paid & confirmed orders</span>
    </div>
</section>
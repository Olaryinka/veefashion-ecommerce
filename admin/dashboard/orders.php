<?php
require_once(__DIR__ . "/includes/dash_header.php");



//    FETCH ALL ORDERS
$stmt = $conn->prepare("
    SELECT 
        orders.id,
        orders.order_number,
        orders.total_amount,
        orders.payment_ref,
        orders.delivery_address,
        orders.status,
        orders.created_at,
        users.fullname,
        users.email,
        users.phone
    FROM orders
    INNER JOIN users ON orders.user_id = users.id
    ORDER BY orders.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();

$sn = 1;
?>

<!-- ORDERS TABLE -->
<section class="orders_section">

    <div class="orders_header">
        <h4 class="table_heading">
            <i class="fa-solid fa-box"></i> All Orders
        </h4>
    </div>

    <div class="table_wrapper">
        <table class="orders_table">

            <thead>
                <tr>
                    <th>SN</th>
                    <th>Order Number</th>
                    <th>User</th>
                    <th>Total Amount</th>
                    <th>Payment Ref</th>
                    <th>Delivery Address</th>
                    <th>Status</th>
                    <th>Date Ordered</th>
                    <!-- <th>Actions</th> -->
                </tr>
            </thead>

            <tbody>

                <?php if ($result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <?php $statusClass = $order['status']; ?>
                        <tr>
                            <td class="sn_id"><?= $sn++; ?></td>

                            <td class="td_id">
                                <?= esc($order['order_number']); ?>
                            </td>

                            <td class="td_name">
                                <strong><?= esc($order['fullname']); ?></strong><br>
                                <small><?= esc($order['email']); ?></small><br>
                                <small><?= esc($order['phone']); ?></small>
                            </td>

                            <td class="td_price">
                                ₦<?= number_format($order['total_amount'], 2); ?>
                            </td>

                            <td>
                                <?= esc($order['payment_ref']); ?>
                            </td>

                            <td class="td_location">
                                <?= esc($order['delivery_address']); ?>
                            </td>

                            <td>
                                 <span class="badge <?= $statusClass ?>"><?= esc($order['status']); ?></span>
                              
                            </td>

                            <td class="td_date">
                                <?= date("M d, Y", strtotime($order['created_at'])); ?>
                            </td>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;">
                            No orders found
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>
    </div>

</section>
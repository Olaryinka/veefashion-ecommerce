<?php
require_once("./includes/user_header.php");

//    FETCH ALL ORDERS
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("
    SELECT
        o.id AS order_id,
        o.order_number,
        o.status,
        o.created_at,
        o.delivery_address,

        oi.qty,
        oi.price,
        oi.item_subtotal,

        p.name AS product_name,

        u.fullname,
        u.email

    FROM orders o
    INNER JOIN order_items oi ON oi.order_id = o.id
    INNER JOIN products p ON p.id = oi.product_id
    INNER JOIN users u ON u.id = o.user_id

    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


$sn = 1;
?>
<!-- ORDERS TABLE -->
<section class="orders_section">

    <div class="orders_header">
        <h4 class="table_heading"><i class="fa-solid fa-box"></i> My Orders</h4>
    </div>

    <div class="table_wrapper">
        <table class="orders_table">

            <thead>
                <tr>
                    <th>SN</th>
                    <th>Order ID</th>
                    <th>User Details</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Item Subtotal</th>
                    <th>Destination</th>
                    <th>Status</th>
                    <th>Date Ordered</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <?php $statusClass = $order['status']; ?>

                        <tr>
                            <td class="td_id" data-label="SN"><?= $sn++; ?></td>
                            <td class="td_id" data-label="Order ID"><?= esc($order['order_number']) ?></td>
                            <td class="td_name" data-label="User Details"><?= esc($order['fullname']) ?><br><small><?= esc($order['email']) ?></small>
                            </td>
                            <td class="td_product-name" data-label="Product Name"><?= esc($order['product_name']) ?></td>
                            <td data-label="Qty"><?= esc($order['qty']) ?></td>
                            <td class="td_price" data-label="Price">₦<?= number_format(esc($order['price'], 2))  ?></td>
                            <td class="td_price" data-label="Item Subtotal">₦<?= number_format(esc($order['item_subtotal'], 2))  ?></td>
                            <td class="td_location" data-label="Destination"><?= esc($order['delivery_address']) ?></td>

                            <td  data-label="Status">
                                <span class="badge <?= $order['status']; ?>">
                                    <?= esc($order['status']) ?>
                                </span>
                            </td>

                            <td class="td_date" data-label="Date Ordered"><?= date("M d, Y", strtotime($order['created_at'])); ?></td>
                            <td class="action_icons" data-label="Actions">
                                <a href="<?= BASE_URL ?>/user/view-order?id=<?= $order['order_id']; ?>" class="order_view_btn"><i
                                        class="fa-solid fa-eye"></i></a>
                            </td>
                        </tr>
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
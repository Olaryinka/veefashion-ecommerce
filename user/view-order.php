<?php
require_once("./includes/user_header.php");


if (!isset($_GET['id'])) {
    die("Invalid order");
}
$order_id = (int) $_GET['id'];
$user_id  = $_SESSION['id'];

// Fetch order and user info
$stmt = $conn->prepare("
    SELECT
        o.id,
        o.order_number,
        o.status,
        o.created_at,
        o.total_amount,
        o.delivery_address,

        u.fullname,
        u.email,
        u.phone,
        u.image

    FROM orders o
    INNER JOIN users u ON u.id = o.user_id
    WHERE o.id = ? AND o.user_id = ?
    LIMIT 1
");

$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found");
}

// Fetch order items
$stmtItems = $conn->prepare("
    SELECT
        oi.qty,
        oi.price,
        oi.item_subtotal,
        p.name
    FROM order_items oi
    INNER JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
");

$stmtItems->bind_param("i", $order_id);
$stmtItems->execute();
$orderItems = $stmtItems->get_result();


$userImage = (!empty($order['image']) && file_exists(
    $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/" . $order['image']
))
    ? BASE_URL . "/assets/images/profile_upload/" . $order['image']
    : BASE_URL . "/assets/images/avatar.jpg";
?>
<div class="order_view_container">

    <!-- PAGE HEADER -->
    <div class="order_header">
        <h2><i class="fa-solid fa-box"></i> Order Details</h2>
        <a href="orders.php" class="back_btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Orders
        </a>
    </div>

    <!-- ORDER SUMMARY -->
    <div class="order_summary_box">
        <div>
            <h4>Order ID</h4>
            <p><?= esc($order['order_number']); ?></p>
        </div>
        <div>
            <h4>Status</h4>
            <span class="badge <?= $order['status']; ?>">
                <?= $order['status']; ?>
            </span>
        </div>
        <div>
            <h4>Date Ordered</h4>
            <p><?= date("M, d, Y", strtotime($order['created_at'])); ?></p>
        </div>
        <div>
            <h4>Total Amount</h4>
            <p class="amount">₦<?= esc(number_format($order['total_amount'])); ?></p>
        </div>
    </div>

    <!-- CUSTOMER INFORMATION -->
    <div class="section_box">
        <h3 class="section_title">Customer Information</h3>

        <div class="user_box">
            <img src="<?= $userImage ?>" alt="customer" class="user_avatar">
            <div>
                <h4><?= esc($order['fullname']); ?></h4>
                <p>Email: <?= esc($order['email']); ?></p>
                <p>Phone: <?= esc($order['phone']); ?></p>
            </div>
        </div>
    </div>

    <!-- DELIVERY INFORMATION -->
    <div class="section_box">
        <h3 class="section_title">Delivery Information</h3>

        <p class="delivery_text">
            <?= nl2br(esc($order['delivery_address'])); ?>
        </p>
    </div>

    <!-- ORDER ITEMS -->
    <div class="section_box">
        <h3 class="section_title">Items in this Order</h3>

        <table class="items_table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($item = $orderItems->fetch_assoc()): ?>
                <tr>
                    <td data-label="Product"><?=esc($item['name']); ?></td>
                    <td data-label="Qty"><?= $item['qty']; ?></td>
                    <td data-label="Price">₦<?= esc(number_format($item['price'], 2)); ?></td>
                    <td data-label="Subtotal">₦<?= esc(number_format($item['item_subtotal'], 2)); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="order_actions">
         <?php if ($order['status'] === 'Pending'): ?>
        <button class="btn cancelOrder_btn" data-id="<?= $order_id; ?>">
            <i class="fa-solid fa-xmark"></i> Cancel Order
        </button>
         <?php else: ?>
        <button class="btn cancelOrder_btn disabled" disabled>
            <i class="fa-solid fa-lock"></i> Cannot Cancel
        </button>
    <?php endif; ?>
    </div>
   

</div>

</div>
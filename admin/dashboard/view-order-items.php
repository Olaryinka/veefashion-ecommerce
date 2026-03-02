<?php
require_once(__DIR__ . "/includes/dash_header.php");

if (!isset($_GET['order_id'])) {
    die("Invalid order");
}

$order_id = (int) $_GET['order_id'];

$stmt = $conn->prepare("
    SELECT 
        o.id,
        o.order_number,
        o.total_amount,
        o.status,
        o.created_at,
        o.delivery_address,
        u.fullname,
        u.email,
        u.phone,
        u.image
    FROM orders o
    INNER JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found");
}
$isFinalized = in_array($order['status'], ['Delivered', 'Cancelled']);
$stmtItems = $conn->prepare("
    SELECT 
        oi.qty,
        oi.price,
        oi.item_subtotal,
        p.name,
        p.image
    FROM order_items oi
    INNER JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmtItems->bind_param("i", $order_id);
$stmtItems->execute();
$orderItems = $stmtItems->get_result();
?>
<?php
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/";
if (!empty($order['image']) && file_exists($uploadDir . $order['image'])) {
    $userImage = BASE_URL . "/assets/images/profile_upload/" . $order['image'];
} else {
    $userImage = BASE_URL . "/assets/images/avatar.jpg";
}

?>

<div class="order_view_container">
    <!-- PAGE HEADER -->
    <div class="order_header">
        <h2><i class="fa-solid fa-cart-flatbed"></i> Order Items Details</h2>
        <a href="order_items.php" class="back_btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Order Items
        </a>
    </div>

    <!-- ORDER SUMMARY -->
    <div class="order_summary_box">
        <div>
            <h4>Order Number</h4>
            <p><?= esc($order['order_number']); ?></p>
        </div>
        <div>
            <h4>Status</h4>
            <span class="badge <?= $order['status']; ?>">
                <?= esc($order['status']); ?>
            </span>
        </div>
        <div>
            <h4>Date Ordered</h4>
            <p><?= date("M d, Y", strtotime($order['created_at'])); ?></p>
        </div>
        <div>
            <h4>Total Amount</h4>
            <p class="amount">₦<?= number_format($order['total_amount']); ?></p>
        </div>
    </div>

    <!-- CUSTOMER INFORMATION -->
    <div class="section_box">
        <h3 class="section_title">Customer Information</h3>

        <div class="user_box">
            <img src="<?= esc($userImage)?>" alt="customer" class="user_avatar">
            <div>
                <h4><?= esc($order['fullname']); ?></h4>
                <p><strong>Email:</strong> <?= esc($order['email']); ?></p>
                <p><strong>Phone:</strong> <?= esc($order['phone']); ?></p>
            </div>
        </div>
    </div>

    <!-- DELIVERY INFORMATION -->
    <div class="section_box">
        <h3 class="section_title">Delivery Information</h3>

        <p class="delivery_text">
            <?= esc($order['delivery_address']); ?>
        </p>
    </div>

    <!-- ORDER ITEMS -->
    <div class="section_box">
        <h3 class="section_title">Items in this Order</h3>

        <table class="items_table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($item = $orderItems->fetch_assoc()): ?>
                    <tr>
                        <td><?= esc($item['name']); ?></td>
                        <td>
                            <img src="<?= BASE_URL ?>/assets/images/products-images/<?= esc($item["image"]); ?>" class="product_thumb">
                        </td>
                        <td><?= $item["qty"]; ?></td>
                        <td>₦<?= number_format($item["price"], 2); ?></td>
                        <td>₦<?= number_format($item["item_subtotal"], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="order_actions">
        <button class="btn mark_delivered"
            data-id="<?= $order_id; ?>"
            <?= $isFinalized ? 'disabled' : ''; ?>>
            <i class="fa-solid fa-check"></i> Mark as Delivered
        </button>

        <button class="btn cancel_order"
            data-id="<?= $order_id; ?>"
            <?= $isFinalized ? 'disabled' : ''; ?>>
            <i class="fa-solid fa-xmark"></i> Cancel Order
        </button>
    </div>

</div>

</div>
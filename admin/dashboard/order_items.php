<?php
require_once(__DIR__ . "/includes/dash_header.php");



$stmt = $conn->prepare("
    SELECT 
    oi.id,
    oi.order_id,
    oi.product_id,
    oi.qty,
    oi.price,
    oi.item_subtotal,
    oi.created_at,
    p.name,
    p.image,
    o.order_number,
    o.status
    FROM order_items oi
    INNER JOIN products p ON oi.product_id = p.id
    INNER JOIN orders o ON oi.order_id = o.id
    ORDER BY oi.created_at DESC
");
$stmt->execute();
$orderItems = $stmt->get_result();
?>
<!-- ORDERS TABLE -->
<section class="orders_section">

    <div class="orders_header">
        <h4 class="table_heading"><i class="fa-solid fa-cart-flatbed"></i> Order Items</h4>
    </div>

    <div class="table_wrapper">
        <table class="orders_table">

            <thead>
                <tr>
                    <th>SN</th>
                    <th>Order Number</th>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Item Subtotal</th>
                    <th>Date Ordered</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($orderItems->num_rows > 0): ?>
                    <?php $sn = 1; ?>
                    <?php while ($item = $orderItems->fetch_assoc()): ?>
                        <tr>
                            <td class="sn_id"><?= $sn++; ?></td>
                            <td class="td_id"><?= esc($item['order_number']); ?></td>
                            <td><?= $item['product_id']; ?></td>
                            <td class="td_product-name"><?= esc($item['name']); ?></td>
                            <td>
                                <img
                                    src="<?= BASE_URL ?>/assets/images/products-images/<?= esc($item['image']); ?>"
                                    class="product_thumb"
                                    alt="<?= esc($item['name']); ?>">
                            </td>
                            <td class="td_price">₦<?= number_format($item['price'], 2); ?></td>
                            <td><?= $item['qty']; ?></td>
                            <td class="td_price">₦<?= number_format($item['item_subtotal'], 2); ?></td>
                            <td class="td_date"><?= date("M d, Y", strtotime($item['created_at'])); ?></td>
                            <td class="action_icons">
                                <a href="<?= BASE_URL ?>/admin/dashboard/view-order-items.php?order_id=<?= $item['order_id'] ?>" class="order_view_btn"><i
                                        class="fa-solid fa-eye"></i></a>
                                <button type="button" class="order_delete_btn"
                                    data-id="<?= $item['id']; ?>"
                                    <?= $item['status'] !== 'Pending' ? 'disabled' : ''; ?>>
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align:center;">
                            No order items found
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </div>
</section>
<?php
require_once(__DIR__ . "/includes/dash_header.php");


$stmt = $conn->prepare("
SELECT
id,
name,
description,
price,
quantity,
status,
image,
created_at
FROM products
WHERE is_deleted = 0
ORDER BY created_at DESC
");
$stmt->execute();
$products = $stmt->get_result();
?>
<div class="products_container">
    <!-- PAGE HEADING -->
    <div class="products_topbar">
        <h2 class="dash_page_title">Manage Products</h2>

        <div class="topbar_actions">
            <a href="<?= BASE_URL ?>/admin/dashboard/add_product.php" class="add_product_btn">
                <i class="fa-solid fa-plus"></i> Add Product
            </a>
        </div>
    </div>

    <!-- PRODUCT TABLE -->
    <div class="products_table_wrapper">
        <table class="products_table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($products->num_rows > 0): ?>
                    <?php $sn = 1; ?>
                    <?php while ($product = $products->fetch_assoc()): ?>
                         <?php $statusClass = str_replace(' ', '-', $product['status']); ?>
                        <tr>
                            <td><?= $sn++; ?> </td>
                            <td><?= $product['id']; ?></td>
                            <td>
                                <?php if (!empty($product['image'])): ?>
                                    <img
                                        src="<?= BASE_URL ?>/assets/images/products-images/<?= esc($product['image']); ?>"
                                        class="product_thumb"
                                        alt="<?= esc($product['name']); ?>">
                                <?php else: ?>
                                    <span>No image</span>
                                <?php endif; ?>
                            </td>

                            <td><?= $product['name']; ?></td>
                            <td><?= $product['description']; ?></td>
                            <td>₦<?= number_format($product['price'], 2); ?></td>
                            <td><?= $product['quantity']; ?></td>

                            <td>
                                <span class="status <?= $statusClass ?>">
                                    <?= esc($product['status']); ?>
                                </span>
                            </td>
                            <td><?= date("M d, Y", strtotime($product['created_at'])); ?></td>

                            <td class="action_buttons">
                                <a href="<?= BASE_URL ?>/admin/dashboard/view-product.php?id=<?= $product['id']; ?>" class="view_btn">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/admin/dashboard/edit_product.php?id=<?= $product['id']; ?>" class="edit_btn">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button
                                    class="deleteProduct_btn"
                                    data-id="<?= $product['id']; ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" style="text-align:center;">
                            No products found
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>
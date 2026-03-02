<?php
require_once(__DIR__ . "/includes/dash_header.php");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product");
}

$product_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT 
        id,
        name,
        price,
        description,
        image,
        quantity,
        status,
        created_at,
        updated_at
    FROM products
    WHERE id = ?
    LIMIT 1
");

$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    die("Product not found");
}
$productImage = !empty($product['image'])
    ? $product['image']
    : 'default-product.jpg';


$stmt = $conn->prepare("
SELECT *
FROM products
WHERE id = ? AND is_deleted = 0
LIMIT 1
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();


if (!$product) {
    die("Product not found or has been deleted");
    
}
$statusClass = str_replace(' ', '-', $product['status']); 
?>


<div class="products_container">

    <!-- TOP BAR -->
    <div class="add_product_header">
        <h2 class="dash_page_title">Product Details</h2>

        <a href="<?= BASE_URL ?>/admin/dashboard/products.php" class="back_btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <!-- PRODUCT CARD -->
    <div class="view_product_card">

        <!-- LEFT IMAGE -->
        <div class="view_product_image">
            <img src="<?= BASE_URL ?>/assets/images/products-images/<?= esc($productImage); ?>"
                alt="<?= esc($product['name']); ?>">
        </div>

        <!-- RIGHT DETAILS -->
        <div class="view_product_details">

            <h2 class="product_title"><?= $product['name'] ?></h2>

            <p class="product_price"><strong>Price: </strong>₦<?= number_format($product['price']) ?></p>

            <p class="product_desc"><strong>Desc:</strong> <?= $product['description'] ?></>

            <p><strong>Status:</strong>
                <span class="status <?= $statusClass ?>">
                    <?= esc($product['status']); ?>
                </span>
            </p>

            <p><strong>Quantity:</strong> <?= $product["quantity"] ?> items</p>

            <p><strong>Date Created: </strong> <?= date("m-d-Y", strtotime($product['created_at'])); ?></p>
            <p><strong>Last Updated: </strong><?= date("m-d-Y", strtotime($product['updated_at'])); ?></p>

        </div>

    </div>

</div>
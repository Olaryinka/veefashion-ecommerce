<?php
require_once(__DIR__ . "/includes/dash_header.php");

//  VALIDATE PRODUCT ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product");
}

$product_id = (int) $_GET['id'];

// FETCH PRODUCT (ONLY IF NOT DELETED)

$stmt = $conn->prepare("
    SELECT 
        id,
        name,
        price,
        description,
        quantity,
        status,
        image
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
?>


<div class="products_container">

    <div class="add_product_header">
        <h2 class="dash_page_title">Edit Product</h2>

        <a href="<?= BASE_URL ?>/admin/dashboard/products.php" class="back_btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <form class="add_product_form" enctype="multipart/form-data" method="POST">
        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">

        <!-- PRODUCT NAME -->
        <div class="input_group">
            <label>Product Name</label>
            <input type="text" name="product_name"
                value="<?= $product['name'] ?>">
        </div>

        <!-- PRICE -->
        <div class="input_group">
            <label>Price (₦)</label>
            <input type="number" name="price"
                value="<?= $product['price'] ?>">
        </div>
        <!-- QTY -->
        <div class="input_group">
            <label>Qty</label>
            <input type="number" name="qty"
                value="<?= $product['quantity'] ?>">
        </div>
        <!-- STATUS -->
        <div class="input_group">
            <!-- <label>Status</label>
            <input type="text" name="status"
                value="<?= $product['status'] ?>"> -->
            <label>Status</label>
            <select name="status">
                <option value="In Stock" <?= $product['status'] === 'In Stock' ? 'selected' : '' ?>>In Stock</option>
                <option value="Out of Stock" <?= $product['status'] === 'Out of Stock' ? 'selected' : '' ?>>Out of Stock</option>
            </select>
        </div>

        <!-- DESCRIPTION -->
        <div class="input_group">
            <label>Description</label>
            <textarea name="description" rows="5"><?= $product['description'] ?></textarea>
        </div>

        <!-- IMAGE UPLOAD -->
        <div class="input_group">
            <label>Product Image</label>
            <input type="file" name="image" class="imgUpload" accept=".jpg,.jpeg,.png">
        </div>

        <!-- IMAGE PREVIEW -->
        <div class="img_preview_container">
            <img id="previewImg" src="<?= BASE_URL ?>/assets/images/products-images/<?= esc($product['image']); ?>">
        </div>

        <button type="submit" class="save_product_btn">
            <i class="fa-solid fa-pen"></i> Update Product
        </button>

    </form>

</div>

<script>
    // Edit product image upload
    // document.querySelector(".imgUpload").addEventListener("change", function(e){
    //     const file = e.target.files[0];
    //     if(file){
    //         document.getElementById("previewImg").src = URL.createObjectURL(file);
    //     }
    // });

    // 
</script>
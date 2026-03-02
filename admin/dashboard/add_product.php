<?php
require_once(__DIR__."/includes/dash_header.php");
?>
<div class="products_container">

    <div class="add_product_header">
        <h2 class="dash_page_title">Add New Product</h2>
        <a href="<?= BASE_URL?>/admin/dashboard/products.php" class="back_btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Products
        </a>
    </div>

    <form class="addProductForm" enctype="multipart/form-data" method="POST">

        <!-- PRODUCT NAME -->
        <div class="input_group">
            <label>Product Name</label>
            <input type="text" name="product_name" placeholder="Enter product name...">
        </div>

        <!-- PRODUCT PRICE -->
        <div class="input_group">
            <label>Price (₦)</label>
            <input type="number" name="price" placeholder="Enter price...">
        </div>

        <!-- PRODUCT QTY -->
        <div class="input_group">
            <label>Qty</label>
            <input type="number" name="qty" placeholder="Enter Qty...">
        </div>
        <!-- PRODUCT STATUS -->
        <div class="input_group">
            <label>Status</label>
            <input type="text" name="status" placeholder="Enter Status...">
        </div>
        

        <!-- DESCRIPTION -->
        <div class="input_group">
            <label>Description</label>
            <textarea name="description" rows="5" placeholder="Write product description..."></textarea>
        </div>

        <!-- IMAGE UPLOAD -->
        <div class="input_group">
            <label>Product Image</label>
            <input type="file" name="image" class="imgUpload" accept=".jpg,.jpeg,.png">
        </div>

        <!-- IMAGE PREVIEW -->
        <div class="img_preview_container">
            <img id="previewImg" src="<?= BASE_URL ?>/assets/images/placeholder.jpg" alt="product_image">
        </div>

        <button type="submit" class="save_product_btn">
            <i class="fa-solid fa-check"></i> Add Product
        </button>

    </form>

</div>



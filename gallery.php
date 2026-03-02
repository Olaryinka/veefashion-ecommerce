<?php
require_once('./layout/header.php');
$cartItems = $_SESSION['cart'] ?? []; // read cart from session or set empty array
?>

<!-- Marquee section -->
<div class="marquee"
    data-text="👜 New Arrivals | 🎉 Fashion Week Starts Next Month | 💄 20% Discount on All Dresses!">
</div>


<section class="gellery-section">

    <div class="grid-container">
        <?php
        // Fetch products from DB
        $stmt = $conn->prepare("SELECT id, name, price, description, image, quantity, is_best_seller, status FROM products WHERE is_best_seller != 1");
        $stmt->execute();
        $result = $stmt->get_result();
        $galleryProducts = [];
        while ($row = $result->fetch_assoc()) {
            $galleryProducts[] = $row;
        }
        foreach ($galleryProducts as $product): ?>
            <div class="gallery-product_container">
                <div class="product-img_wrapper">
                    <img src="<?= BASE_URL ?>/assets/images/products-images/<?= esc($product['image']) ?>" alt="<?=esc( $product['name']) ?>" class="product-img">
                    <?php if ($product['is_best_seller'] == 2): ?>
                        <div class="product-img_text">Best Seller</div>
                    <?php endif; ?>
                </div>
                <div class="product-content_wrapper">
                    <div class="product-details">
                        <p><span>Product Name: <?= esc($product['name']) ?></span></p>
                        <p><span>Price: </span><?= number_format($product['price'], 2) ?></p>
                        <p><span>Description: </span><?= esc($product['description']) ?></p>
                    </div>
                </div>
                <button class="cart-btn" type="button" data-id="<?= $product['id'] ?>">
                    <i class="fa-solid fa-cart-plus carticon"></i> Add to Cart
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php
require_once('./layout/footer.php');
?>
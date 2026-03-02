<?php
require_once('./layout/header.php');
?>

<!-- Marquee section -->
<div class="marquee"
    data-text="👜 New Arrivals | 🎉 Fashion Week Starts Next Month | 💄 20% Discount on All Dresses!">
</div>
<!-- ScrollUp Btn -->
<a href="<?= BASE_URL ?>/index.php#site" class="scrollup_btn"><i class="fa-solid fa-chevron-up scroll-icon"></i></a>

<!-- Banner section -->
<div class="banner">
    <video autoplay muted loop playsinline class="video-bg">
        <source src="<?= BASE_URL ?>/assets/media/fashion-video-1.mp4" type="video/mp4">
    </video>
    <div class="banner-overlay"></div>
    <div class="banner-heading" data-aos="fade-up" data-aos-duration="2000">
        <p><?= esc($WELCOME_TXT); ?></p>
    </div>
    <div class="banner-content">
        <p>Discover timeless fashion<span class="banner-content-span">Crafted for every story.</span></p>
    </div>
    <div class="get_start">
        <a href="<?= BASE_URL ?>/signup.php">Get started</a>
    </div>
</div>
<!-- Best sellers section -->
<section class="seller-section">
    <div class="sellers-heading">
        <h3>OUR BEST SELLERS</h3>
        <h6>Don't Miss Out</h6>
    </div>
    <div class="grid-container">
        <?php
        // Fetch best sellers from DB
        $stmt = $conn->prepare("SELECT id, name, price, description, image, quantity, is_best_seller FROM products WHERE is_best_seller = 1  ORDER BY created_at DESC LIMIT 5");
        $stmt->execute();
        $result = $stmt->get_result();

        $bestSellers = [];
        while ($row = $result->fetch_assoc()) {
            $bestSellers[] = $row;
        }
        foreach ($bestSellers as $product): ?>
            <div class="seller-product_container">
                <div class="product-img_wrapper">
                    <img src="<?= BASE_URL ?>/assets/images/products-images/<?= esc($product['image']) ?>"
                        alt="<?= esc($product['name']) ?>" class="product-img">

                    <?php if ($product['is_best_seller'] == 1): ?>
                        <div class="product-img_text">Best Seller</div>
                    <?php endif; ?>
                </div>

                <div class="product-content_wrapper">
                    <div class="product-details">
                        <p><span>Product Name:</span> <?= esc($product['name']) ?></p>
                        <p><span>Price:</span> ₦<?= number_format($product['price'], 2) ?></p>
                        <p><span>Description:</span> <?= esc($product['description']) ?></p>
                    </div>
                </div>

                <button class="cart-btn" type="button" data-id="<?= $product['id'] ?>">
                    <i class="fa-solid fa-cart-plus carticon"></i> Add to Cart
                </button>
                 <!-- <button class="reset_btn" type="button" id="resetCartBtn">Reset Cart</button> -->
            </div>
        <?php endforeach; ?>

    </div>
    <a href="<?= BASE_URL ?>/gallery.php" class="explore-btn">Explore Collection</a>
</section>
<!-- Choose section -->
<section class="choose-section">
    <div class="choose-container">
        <div class="choose-video_wrpper">
            <video autoplay muted loop playsinline class="choose-video">
                <source src="<?= BASE_URL ?>/assets/media/fashion-video-2.mp4" type="video/mp4">
            </video>
        </div>
        <div class="choose-content">
            <h3 class="choose-heading">Why Choose The Vee-Fashion House</h3>
            <div class="choose-text_wrapper">
                <div class="choose-text">
                    <div class="choose-icon_text">
                        <img src="<?= BASE_URL ?>/assets/icon/checkmark.png" alt="mark-icon" class="mark-icon">
                        <h3>Same Day Availability</h3>
                    </div>
                    <p>Choose us for Same Day Availability – fast, reliable service when you need it most!</p>
                </div>
                <div class="choose-text">
                    <div class="choose-icon_text">
                        <img src="<?= BASE_URL ?>/assets/icon/checkmark.png" alt="mark-icon" class="mark-icon">
                        <h3>Outstanding Support</h3>
                    </div>
                    <p>Experience unparalleled service with our dedicated team, ready to assist you 24/7!</p>

                </div>

            </div>
            <div class="content-links_container">
                <a href="<?= BASE_URL ?>/about.php" class="content-link">Get to know Us</a>
                <a href="<?= BASE_URL ?>/contact.php" class="content-link">Contact Us</a>
            </div>
        </div>
    </div>
</section>
<?php
require_once('./layout/footer.php');
?>
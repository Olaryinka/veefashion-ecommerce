<?php
require_once("./layout/header.php");
$cartItems = $_SESSION['cart'] ?? []; // read cart from session or set empty array
$cartHasItems = !empty($cartItems);
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['qty'] * $item['price'];
}
$total = $subtotal;

if (!is_array($cartItems)) {
    $cartItems = [];
}
?>

<main class="cart_main-container">

    <!-- Cart Page Heading -->
    <section class="heading-container">
        <h3 class="cart-heading">Fashion Items in Your Cart</h3>
        <p class="cart-subheading">Everything you love in one place, checkout when you're ready.</p>
    </section>


    <section class="cart-page">

        <!-- LEFT SIDE (Cart Items) -->
        <aside class="cart-left">

            <div class="heading-wrap">
                <h2 class="heading">My Cart</h2>
                <p><span class="cartTotal"><?= $cartCount ?></span> <?= $cartCount === 1 ? 'Item' : 'Items' ?></p>
            </div>

            <!-- Cart Item empty -->
            <?php if (empty($cartItems)): ?>
                <p class="empty_cart-text">Your cart is empty</p>
            <?php else: ?>

                <?php foreach ($cartItems as $product): ?>
                    <div class="cart-item">
                        <img src="<?= BASE_URL ?>/assets/images/products-images/<?= esc($product['image']) ?>" class="cart-img">
                        <div class="cart-info">
                            <h4 class="cart-name"><?= esc($product['name']) ?></h4>
                            <p class="cart-price">₦<?= number_format($product['price'], 2) ?></p>
                        </div>
                        <div class="cart-quantity">
                            <button class="qty-minus" data-id="<?= $product['id'] ?>">-</button>
                            <span class="qty-input"><?= $product['qty'] ?></span>
                            <button class="qty-plus" data-id="<?= $product['id'] ?>">+</button>
                        </div>
                        <p class="cart-subtotal">₦<?= number_format($product['price'] * $product['qty'], 2) ?></p>
                        <button class="cart-trash" data-id="<?= $product['id'] ?>">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </aside>


        <!-- RIGHT SIDE (Order Summary) -->
        <div class="cart-right">

            <h3 class="cart-summary-heading">Order Summary</h3>

            <div class="summary-row">
                <span class="subtotal">Subtotal</span>
                <span class="subtotal_price">₦<?= number_format($total) ?></span>
            </div>

            <div class="summary-row">
                <span class="delivery">Delivery</span>
                <span class="free">FREE</span>
            </div>


            <div class="summary-total summary-row">
                <span class="summary_total">Total</span>
                <span class="summary_price">₦<?= number_format($total) ?></span>
            </div>

            <!-- Delivery Address -->
            <div class="summary_address">
                <label for="address">Delivery Address:</label>
                <textarea id="address" placeholder="Please enter your delivery address here..."></textarea>
            </div>

            <!-- Checkout Button -->
            <!-- <button class="cart-checkout-btn">Checkout</button> -->
            <button class="cart-checkout-btn" id="checkoutBtn" <?= !$cartHasItems ? 'disabled' : '' ?>>Checkout</button>


            <p class="secure-text">
                <i class="fa-solid fa-lock"></i> Secure Checkout
            </p>

        </div>

    </section>

</main>

<?php
require_once("./layout/footer.php");
?>
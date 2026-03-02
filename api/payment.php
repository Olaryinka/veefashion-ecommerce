<?php
require_once("../layout/header.php");

$orderId = $_SESSION['last_order_id'] ?? 0;

$stmt = $conn->prepare("
    SELECT total_amount 
    FROM orders 
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

$total = $order['total_amount'] ?? 0;
?>

<main id="site">

    <section class="payment-section">
        <h2>Select Payment Method</h2>

        <div class="pay-option-container">

            <!-- Paystack Card Payment -->
            <button id="paystackBtn" class="pay-method" data-email="<?= esc($_SESSION['email']) ?>" data-amount="<?= $total ?>">
                Pay with Card (Paystack)
            </button>

            <!-- Other UI buttons -->
            <!-- <button class="pay-method" data-method="transfer">Bank Transfer</button>
        <button class="pay-method" data-method="ussd">Pay with USSD</button> -->
        </div>

        <p class="total-price">
            Total Amount: <strong>₦<?= number_format($total, 2) ?></strong>
        </p>
    </section>

</main>

<?php require_once("../layout/footer.php"); ?>
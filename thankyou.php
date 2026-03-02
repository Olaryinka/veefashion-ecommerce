<?php 
require_once("./layout/header.php"); 

if (!isset($_GET['order_id'])) {
    die('Invalid order');
}

$order_id = (int) $_GET['order_id'];

$stmt = $conn->prepare("SELECT order_number FROM orders    WHERE id = ? ");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Order not found');
}

$order = $result->fetch_assoc();
$order_number = $order['order_number'];

?>


<section class="thankyou-section" style="text-align:center; padding:50px;">
    <h2>🎉 Order Confirmed!</h2>
    <p>Thank you for shopping with Vee-Fashion House</p>
    <p>Your Order ID is: <strong>#<?= esc($order_number)?></strong></p>

    <a href="<?= BASE_URL ?>/index.php">
        <button class="shopping-btn" style="padding:10px 20px; margin-top:20px;">Continue Shopping</button>
    </a>
</section>

<?php require_once("./layout/footer.php"); ?>
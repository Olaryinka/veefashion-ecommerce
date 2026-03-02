<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$product_id = (int)($_POST['product_id'] ?? 0);

if ($product_id <= 0 || !isset($_SESSION['cart'][$product_id])) {
    echo json_encode(["status" => "error", "message" => "Product not in cart"]);
    exit;
}

// Delete item from cart
unset($_SESSION['cart'][$product_id]);

// Recalculate cart count (sum qty values)
$cartCount = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartCount += $item['qty'];
}

echo json_encode([
    "status" => "success",
    "message" => "Item removed from cart",
    "cart_count" => $cartCount
]);

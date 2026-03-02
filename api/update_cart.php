<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");

// Only accept POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}


$product_id = (int) ($_POST['product_id'] ?? 0);
$type = $_POST['type'] ?? '';

if (!isset($_SESSION['cart'][$product_id])) {
    echo json_encode(["status" => "error", "message" => "Product not in cart"]);
    exit;
}

// Update qty
if ($type === "plus") {
    $_SESSION['cart'][$product_id]['qty']++;
} elseif ($type === "minus" && $_SESSION['cart'][$product_id]['qty'] > 1) {
    $_SESSION['cart'][$product_id]['qty']--;
}

// Calculate values
$itemSubtotal = $_SESSION['cart'][$product_id]['qty'] * $_SESSION['cart'][$product_id]['price'];
$cartSubtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartSubtotal += $item['qty'] * $item['price'];
}

echo json_encode([
    "status" => "success",
    "qty" => $_SESSION['cart'][$product_id]['qty'],
    "item_subtotal" => number_format($itemSubtotal, 2),
    "cart_subtotal" => number_format($cartSubtotal, 2),
    "total" => number_format($cartSubtotal, 2)
]);







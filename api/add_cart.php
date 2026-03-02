<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$product_id = (int) ($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid product"]);
    exit;
}

// Initialize cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch product
$stmt = $conn->prepare("SELECT id, name, price, description, image, quantity, status FROM products WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Product not found"]);
    exit;
}

$product = $result->fetch_assoc();

// No duplicate product
if (isset($_SESSION['cart'][$product_id])) {
    echo json_encode([
        "status" => "error",
        "message" => "Order already in the cart"
    ]);
    exit;
}


$_SESSION['cart'][$product_id] = [
    "id"    => $product['id'],
    "name"  => $product['name'],
    "price" => $product['price'],
    "description" => $product['description'],
    "image" => $product['image'],
    "status"   => $product['status'],
    "qty"   => 1
];


// Count cart items
$cart_count = array_sum(array_column($_SESSION['cart'], 'qty'));
echo json_encode([
    "status" => "success",
    "message" => "Product added to cart",
    "cart_count" => $cart_count
]);

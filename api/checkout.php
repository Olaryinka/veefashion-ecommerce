<?php
session_start();
$root = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root/config/db.php");
require_once("$root/config/function.php");
define('BASE_URL', '/veefashion');


// Must be POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

// Must be logged in
if (!isset($_SESSION['id'])) {
    echo json_encode([
        "status" => "Login required",
        "message" => "Please login to continue",
        "redirect" => BASE_URL . "/signin.php"
    ]);
    exit;
}

$userId = (int)$_SESSION['id'];
$cart = $_SESSION['cart'] ?? [];
$address = esc(trim($_POST['delivery_address'] ?? ''));

// Validate address
if ($address === '') {
    echo json_encode(["status" => "error", "message" => "Delivery address required"]);
    exit;
}

// Validate cart
if (empty($cart)) {
    echo json_encode(["status" => "error", "message" => "Cart is empty"]);
    exit;
}

// Calculate order total
$orderTotal = 0;
foreach ($cart as $item) {
    $orderTotal += $item['qty'] * $item['price'];
}
//  Check if user already has a pending order
$checkStmt = $conn->prepare("
    SELECT id 
    FROM orders 
    WHERE user_id = ? AND status = 'Pending'
    ORDER BY created_at DESC
    LIMIT 1
");
$checkStmt->bind_param("i", $userId);
$checkStmt->execute();
$existingOrder = $checkStmt->get_result()->fetch_assoc();

if ($existingOrder) {
    // Reuse existing order
    $_SESSION['last_order_id'] = $existingOrder['id'];

    echo json_encode([
        "status"  => "success",
        "message" => "You already have a pending order. Continue payment.",
        "redirect" => BASE_URL . "/api/payment.php"
    ]);
    exit;
}

// Generate order number 
$orderNumber = 'VF-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

// Save order
$stmt = $conn->prepare("INSERT INTO orders (order_number, user_id, total_amount, delivery_address) VALUES (?, ?, ?, ?)");
if ($stmt === false) {
    die("SQL Prepare Failed: " . $conn->error);
}
$stmt->bind_param("sids", $orderNumber, $userId, $orderTotal, $address);


if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Order save failed"]);
    exit;
}

$orderId = $conn->insert_id;

// 🔴 IMPORTANT: store order ID for payment verification
$_SESSION['last_order_id'] = $orderId;

// Save order items
$itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, name, price, qty, item_subtotal) VALUES (?, ?, ?, ?, ?, ?)");

foreach ($cart as $item) {
    $pid = (int)$item['id'];
    $name = $item['name'];
    $price = (float)$item['price'];
    $qty = (int)$item['qty'];
    $itemSubtotal = $qty * $price;

    // Correct types: i = int, d = double/float, s = string
    $itemStmt->bind_param("iisdid", $orderId, $pid, $name, $price, $qty, $itemSubtotal);
    $itemStmt->execute();
}


echo json_encode([
    "status"  => "success",
    "message" => "Order placed successfully",
    "redirect" => BASE_URL . "/api/payment.php"
]);

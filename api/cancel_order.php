<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$order_id = (int) ($_POST['order_id'] ?? 0);
$user_id  = $_SESSION['id'];

// Fetch order
$stmt = $conn->prepare("
    SELECT status
    FROM orders
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(["status" => "error", "message" => "Order not found"]);
    exit;
}

// Block invalid cancels
if ($order['status'] !== 'Pending') {
    echo json_encode([
        "status" => "error",
        "message" => "This order can no longer be cancelled"
    ]);
    exit;
}

// Cancel order
$stmt = $conn->prepare("
    UPDATE orders
    SET status = 'Cancelled'
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();

echo json_encode([
    "status" => "success",
    "message" => "Order cancelled successfully"
]);

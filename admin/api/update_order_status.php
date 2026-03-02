<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");
require_once("$root_folder/config/mail.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}
if (
    !isset($_SESSION['admin_id']) ||
    $_SESSION['role'] !== 'Admin' ||
    $_SESSION['admin_role'] !== 'Super'
) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized"
    ]);
    exit;
}
// Validate inputs
$order_id = (int)($_POST['order_id'] ?? 0);
$new_status = $_POST['status'] ?? '';

$allowedStatus = ['Delivered', 'Cancelled'];

if ($order_id <= 0 || !in_array($new_status, $allowedStatus)) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

// Prevent updating already finalized orders
$stmtCheck = $conn->prepare("
    SELECT status FROM orders WHERE id = ? LIMIT 1
");
$stmtCheck->bind_param("i", $order_id);
$stmtCheck->execute();
$current = $stmtCheck->get_result()->fetch_assoc();

if (!$current) {
    echo json_encode(["status" => "error", "message" => "Order not found"]);
    exit;
}

if (in_array($current['status'], ['Delivered', 'Cancelled'])) {
    echo json_encode(["status" => "error", "message" => "Order already finalized"]);
    exit;
}

// Update order
$stmt = $conn->prepare("
    UPDATE orders SET status = ? WHERE id = ?
");
$stmt->bind_param("si", $new_status, $order_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success",
     "message" => "Order updated"]);
} else {
    echo json_encode(["status" => "error", 
    "message" => "Update failed"]);
}
<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");
require_once("$root_folder/config/mail.php"); 

// Only POST allowed
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
// Validate input
$product_id = (int) ($_POST['product_id'] ?? 0);

if ($product_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid product"]);
    exit;
}

// OPTIONAL: prevent deleting products already in order items
$stmtCheck = $conn->prepare("
    SELECT COUNT(*) AS total 
    FROM order_items 
    WHERE product_id = ?
");
$stmtCheck->bind_param("i", $product_id);
$stmtCheck->execute();
$count = $stmtCheck->get_result()->fetch_assoc();

if ($count['total'] > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Product cannot be deleted. It exists in orders."
    ]);
    exit;
}

// Soft delete
$stmt = $conn->prepare("
    UPDATE products 
    SET is_deleted = 1 
    WHERE id = ?
");
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Product deleted successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Delete failed"
    ]);
}

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
$item_id = (int)($_POST['item_id'] ?? 0);


if ($item_id <= 0) {
echo json_encode(["status" => "error", "message" => "Invalid item"]);
exit;
}

/* Check order status */
$stmtCheck = $conn->prepare("
    SELECT o.status
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.id
    WHERE oi.id = ?
");
$stmtCheck->bind_param("i", $item_id);
$stmtCheck->execute();
$order = $stmtCheck->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(["status" => "error", "message" => "Item not found"]);
    exit;
}

/* ONLY Pending orders allowed */
if ($order['status'] !== 'Pending') {
    echo json_encode([
        "status" => "error",
        "message" => "Only Pending orders can be modified"
    ]);
    exit;
}

/* Delete item */
$stmt = $conn->prepare("DELETE FROM order_items WHERE id = ?");
$stmt->bind_param("i", $item_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Order item deleted"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Delete failed"
    ]);
}
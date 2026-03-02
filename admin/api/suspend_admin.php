<?php
session_start();
$root = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root/config/db.php");
require_once("$root/config/function.php");

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

$admin_id = (int) ($_POST['admin_id'] ?? 0);

if ($admin_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid user"]);
    exit;
}

// Toggle status
$stmt = $conn->prepare("
    UPDATE admins 
    SET status = IF(status = 'Active', 'Suspended', 'Active')
    WHERE id = ? AND is_deleted = 0
");
$stmt->bind_param("i", $admin_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Admin status updated"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update status"
    ]);
}

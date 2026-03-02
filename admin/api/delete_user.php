
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
$user_id = (int) ($_POST['user_id'] ?? 0);

if ($user_id <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid user"]);
    exit;
}

$stmt = $conn->prepare("
    UPDATE users 
    SET is_deleted = 1
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "User deleted successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to delete user"
    ]);
}

<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$message  = trim($_POST['message'] ?? '');

if ($fullname === '' || $email === '' || $phone === '' || $message === '') {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}
$enquiryCode = "ENQ-" . date("Ymd") . "-" . rand(1000, 9999);

$stmt = $conn->prepare(
    "INSERT INTO enquiries (enquiry_code, fullname, email, phone, message, status) VALUES (?,?, ?, ?, ?, 'New')"
);

$stmt->bind_param("sssss",$enquiryCode, $fullname, $email, $phone, $message);

if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Failed to send enquiry"]);
    exit;
}

echo json_encode([
    "status" => "success",
    "message" => "Your enquiry has been sent successfully"
]);

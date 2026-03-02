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
        "message" => "You are not authorized to reply this message."
    ]);
    exit;
}
$enquiryId = (int)$_POST['enquiry_id'];
$email     = trim($_POST['email']);
$message   = trim($_POST['reply_message']);

if ($message === '') {
    echo json_encode(["status" => "error", "message" => "Reply message required"]);
    exit;
}

// Email content
$subject = "Re: Your enquiry with VeeFashion House";

$body = "
    <p>Thank you for contacting <strong>Vee Fashion House</strong>.</p>
    <p><strong>Our reply:</strong></p>
    <p>{$message}</p>
    <br>
    <p>Best regards,<br>Vee Fashion Support</p>
";

// Send email
if (!sendMail($email, '', $subject, $body)) {
    echo json_encode(["status" => "error", "message" => "Email failed"]);
    exit;
}

// Update enquiry status
$stmt = $conn->prepare("UPDATE enquiries SET status = 'Replied' WHERE id = ? AND status != 'Replied'");
$stmt->bind_param("i", $enquiryId);
$stmt->execute();

echo json_encode([
    "status" => "success",
    "message" => "Reply sent successfully"
]);

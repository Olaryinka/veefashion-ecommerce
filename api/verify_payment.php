<?php
session_start();
$root = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
define('BASE_URL', '/VeeFashion');
require_once("$root/config/db.php");
require_once("$root/config/function.php");
require_once("$root/config/mail.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "error", "message" => "Login required"]);
    exit;
}

$userId = (int)$_SESSION['id'];
$reference = trim($_POST['reference'] ?? '');
$orderId = (int)($_SESSION['last_order_id'] ?? 0);

if ($orderId <= 0) {
    echo json_encode(["status" => "error", "message" => "Order not found. Please checkout again."]);
    exit;
}

if ($reference === '') {
    echo json_encode(["status" => "error", "message" => "Payment reference required"]);
    exit;
}

$PAYSTACK_SECRET_KEY = $_ENV['PAYSTACK_SECRET_KEY'];

// --- Verify payment from Paystack ---
$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . $reference,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . $PAYSTACK_SECRET_KEY, // 🔴 Replace with your secret key
        "Cache-Control: no-cache",
    ],
]);

$response = curl_exec($curl);
curl_close($curl);

$data = json_decode($response, true);

if (!$data || !$data['status']) {
    echo json_encode(["status" => "error", "message" => "Unable to verify payment"]);
    exit;
}

if ($data['data']['status'] !== 'success') {
    echo json_encode(["status" => "error", "message" => "Payment not successful"]);
    exit;
}

// --- Fetch order total from DB ---
$orderStmt = $conn->prepare("SELECT total_amount FROM orders WHERE id = ? AND user_id = ? LIMIT 1");
$orderStmt->bind_param("ii", $orderId, $userId);
$orderStmt->execute();
$result = $orderStmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "status" => "error",
        "message" => "Order not found or unauthorized"
    ]);
    exit;
}

$order = $result->fetch_assoc();
$dbAmount = (float)$order['total_amount'];

// --- Validate amount paid ---
$paidAmountKobo = (int)$data['data']['amount'];
$dbAmountKobo   = (int) round($dbAmount * 100);

if ($paidAmountKobo !== $dbAmountKobo) {
    echo json_encode([
        "status" => "error",
        "message" => "Payment amount mismatch. Payment rejected."
    ]);
    exit;
}
// --- Prevent duplicate payment ---
$checkStmt = $conn->prepare("SELECT status FROM orders  WHERE id = ?");
$checkStmt->bind_param("i", $orderId);
$checkStmt->execute();
$check = $checkStmt->get_result()->fetch_assoc();

if ($check['status'] === 'Paid') {
    echo json_encode([
        "status" => "error",
        "message" => "Order already paid"
    ]);
    exit;
}
// --- Update the existing order ---
$status = "Paid";
$updateStmt = $conn->prepare("UPDATE orders SET status = ?, payment_ref = ? WHERE id = ? AND user_id = ?");
$updateStmt->bind_param("ssii", $status, $reference, $orderId, $userId);

if (!$updateStmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Order update failed", "sql_error" => $updateStmt->error]);
    exit;
}
// --- Fetch order + user info for email ---
$stmt = $conn->prepare("
    SELECT 
    u.fullname, u.email,
    o.order_number, o.total_amount, o.delivery_address
    FROM orders o
    INNER JOIN users u ON u.id = o.user_id
    WHERE o.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $orderId);
$stmt->execute();
$orderData = $stmt->get_result()->fetch_assoc();

if ($orderData) {

    $toEmail = $orderData['email'];
    $toName  = $orderData['fullname'];

    $subject = "Order Confirmed {$orderData['order_number']}";

    $body = "
    <h2>🎉 Order Successful!</h2>

    <p>Hi <strong>{$toName}</strong>,</p>

    <p>Thank you for shopping with <strong>Vee Fashion House</strong>.</p>

    <p>Your order has been <strong>successfully confirmed</strong>.</p>

    <hr>

    <p><strong>Order Number:</strong> {$orderData['order_number']}</p>
    <p><strong>Total Paid:</strong> ₦" . number_format($orderData['total_amount'], 2) . "</p>
    <p><strong>Delivery Address:</strong><br>{$orderData['delivery_address']}</p>

    <hr>

    <p>You can track your order anytime from your dashboard.</p>
    <p>We’ll notify you once your order is shipped 🚚</p>

    <br>

    <p>Best regards,<br><strong>Vee Fashion House</strong></p>
    ";

    //  Send email (DO NOT block payment if it fails)
    sendMail($toEmail, $toName, $subject, $body);
}

// --- Clear cart after successful payment ---
$_SESSION['cart'] = [];
$_SESSION['last_order_id'] = $orderId;

echo json_encode([
    "status"  => "success",
    "message" => "Payment verified and order confirmed",
    "redirect" => BASE_URL . "/thankyou.php?order_id=" . $orderId
]);

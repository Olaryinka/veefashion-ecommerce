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
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

/* Collect POST data */
$site_name = trim($_POST['site_name'] ?? '');
$email     = trim($_POST['contact_email'] ?? '');
$phone     = trim($_POST['contact_phone'] ?? '');
$text      = trim($_POST['welcome_text'] ?? '');

if (!$site_name || !$email || !$phone) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}

/* Fetch original settings */
$stmt = $conn->prepare("SELECT * FROM settings LIMIT 1");
$stmt->execute();
$original = $stmt->get_result()->fetch_assoc();

if (!$original) {
    echo json_encode(["status" => "error", "message" => "Settings not found"]);
    exit;
}

/* Detect changes */
$hasChanges = false;

if (
    $site_name !== $original['site_name'] ||
    $email     !== $original['contact_email'] ||
    $phone     !== $original['contact_phone'] ||
    $text      !== $original['welcome_text']
) {
    $hasChanges = true;
}

/* Logo changed? */
if (!empty($_FILES['logo']['name'])) {
    $hasChanges = true;
}

if (!$hasChanges) {
    echo json_encode([
        "status" => "error",
        "message" => "No changes detected"
    ]);
    exit;
}

/* Handle logo upload */
$logo = $original['logo'];

if (!empty($_FILES['logo']['name'])) {
    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(["status" => "error", "message" => "Invalid logo type"]);
        exit;
    }

    $logo = "logo_" . time() . "_" . uniqid() . "." . $ext;

    if (!move_uploaded_file(
        $_FILES['logo']['tmp_name'],
        "$root/assets/icon/$logo"
    )) {
        echo json_encode(["status" => "error", "message" => "Logo upload failed"]);
        exit;
    }
}

/* Update settings */
$stmt = $conn->prepare("
    UPDATE settings SET
        site_name = ?,
        contact_email = ?,
        contact_phone = ?,
        welcome_text = ?,
        logo = ?
    WHERE id = ?
");

$stmt->bind_param(
    "sssssi",
    $site_name,
    $email,
    $phone,
    $text,
    $logo,
    $original['id']
);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Settings updated successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Update failed"
    ]);
}

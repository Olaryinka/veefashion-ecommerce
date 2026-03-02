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

//   Collect POST data FIRST

$user_id  = (int) ($_POST['user_id'] ?? 0);
$fullname = trim($_POST['fullname'] ?? '');
$email    = strtolower(trim($_POST['email'] ?? ''));
$phone    = trim($_POST['phone'] ?? '');
$role     = trim($_POST['role'] ?? '');
$status   = trim($_POST['status'] ?? '');

if ($user_id <= 0 || !$fullname || !$email || !$phone || !$role || !$status) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}


//  Fetch original user data

$stmt = $conn->prepare("
    SELECT fullname, email, phone, role, status, image
    FROM users
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$original = $stmt->get_result()->fetch_assoc();

if (!$original) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}


// Detect changes

$hasChanges = false;

// Compare text fields
if (
    $fullname !== $original['fullname'] ||
    $email    !== $original['email'] ||
    $phone    !== $original['phone'] ||
    $role     !== $original['role'] ||
    $status   !== $original['status']
) {
    $hasChanges = true;
}

// Image changed?
if (!empty($_FILES['image']['name'])) {
    $hasChanges = true;
}

if (!$hasChanges) {
    echo json_encode([
        "status" => "error",
        "message" => "No changes detected"
    ]);
    exit;
}


//  Handle image upload (if any)

$imageName = $original['image'];

if (!empty($_FILES['image']['name'])) {
    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(["status" => "error", "message" => "Invalid image type"]);
        exit;
    }

    $imageName = time() . "_" . uniqid() . "." . $ext;

    if (!move_uploaded_file(
        $_FILES['image']['tmp_name'],
        "$root/assets/images/profile_upload/" . $imageName
    )) {
        echo json_encode(["status" => "error", "message" => "Image upload failed"]);
        exit;
    }
}


// Update user

$stmt = $conn->prepare("
    UPDATE users SET
        fullname = ?,
        email = ?,
        phone = ?,
        role = ?,
        status = ?,
        image = ?
    WHERE id = ?
");

$stmt->bind_param(
    "ssssssi",
    $fullname,
    $email,
    $phone,
    $role,
    $status,
    $imageName,
    $user_id
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "User updated successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Update failed"]);
}

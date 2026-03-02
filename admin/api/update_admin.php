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

$admin_id  = (int) ($_POST['admin_id'] ?? 0);
$fullname = trim($_POST['fullname'] ?? '');
$email    = strtolower(trim($_POST['email'] ?? ''));
$role     = trim($_POST['role'] ?? '');
$status   = trim($_POST['status'] ?? '');

if ($admin_id <= 0 || !$fullname || !$email || !$role || !$status) {
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}


//  Fetch original admin data

$stmt = $conn->prepare("
    SELECT fullname, email,  role, status, image
    FROM admins
    WHERE id = ?
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$original = $stmt->get_result()->fetch_assoc();

if (!$original) {
    echo json_encode(["status" => "error", "message" => "Admin not found"]);
    exit;
}


    // Detect changes

$hasChanges = false;

// Compare text fields
if (
    $fullname !== $original['fullname'] ||
    $email    !== $original['email'] ||
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


// Update admin

$stmt = $conn->prepare("
    UPDATE admins SET
        fullname = ?,
        email = ?,
        role = ?,
        status = ?,
        image = ?
    WHERE id = ?
");

$stmt->bind_param(
    "sssssi",
    $fullname,
    $email,
    $role,
    $status,
    $imageName,
    $admin_id
);

if ($stmt->execute()) {
    echo json_encode(["status" => "success",
     "message" => "Admin updated successfully"]);
} else {
    echo json_encode(["status" => "error", 
    "message" => "Update failed"]);
}

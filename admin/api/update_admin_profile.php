<?php
session_start();
$root = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root/config/db.php");
require_once("$root/config/function.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$admin_id = (int) $_SESSION['admin_id'];

/* FETCH CURRENT ADMIN */
$stmt = $conn->prepare("
    SELECT fullname, email, password, image
    FROM admins
    WHERE id = ?
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

if (!$current) {
    echo json_encode(["status" => "error", "message" => "Admin not found"]);
    exit;
}

/* INPUTS (ALLOW EMPTY) */
$fullname = trim($_POST['fullname'] ?? '');
$email    = strtolower(trim($_POST['email'] ?? ''));

$oldPass  = $_POST['old_password'] ?? '';
$newPass  = $_POST['new_password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

$hasChanges = false;
$passwordChanged = false;

/* KEEP CURRENT VALUES */
$finalName  = $current['fullname'];
$finalEmail = $current['email'];
$imageName  = $current['image'];

/* TEXT CHANGES */
if ($fullname !== '' && $fullname !== $current['fullname']) {
    $finalName = $fullname;
    $hasChanges = true;
}

if ($email !== '' && $email !== $current['email']) {
    $finalEmail = $email;
    $hasChanges = true;
}

/* IMAGE CHANGE */
if (!empty($_FILES['image']['name'])) {

    $allowed = ['jpg','jpeg','png'];
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

    $hasChanges = true;
}

/* PASSWORD CHANGE (OPTIONAL BUT STRICT) */
if ($oldPass || $newPass || $confirm) {

    if (!$oldPass || !$newPass || !$confirm) {
        echo json_encode(["status"=>"error","message"=>"All password fields are required"]);
        exit;
    }

    if (!password_verify($oldPass, $current['password'])) {
        echo json_encode(["status"=>"error","message"=>"Old password is incorrect"]);
        exit;
    }

    if ($newPass !== $confirm) {
        echo json_encode(["status"=>"error","message"=>"Passwords do not match"]);
        exit;
    }

    if (password_verify($newPass, $current['password'])) {
        echo json_encode(["status"=>"error","message"=>"New password must be different"]);
        exit;
    }

    $newHashedPassword = password_hash($newPass, PASSWORD_DEFAULT);
    $passwordChanged = true;
    $hasChanges = true;
}

/*  NO CHANGES */
if (!$hasChanges) {
    echo json_encode([
        "status" => "error",
        "message" => "No changes detected"
    ]);
    exit;
}

/* UPDATE */
if ($passwordChanged) {

    $stmt = $conn->prepare("
        UPDATE admins SET
            fullname = ?,
            email = ?,
            image = ?,
            password = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "ssssi",
        $finalName,
        $finalEmail,
        $imageName,
        $newHashedPassword,
        $admin_id
    );

} else {

    $stmt = $conn->prepare("
        UPDATE admins SET
            fullname = ?,
            email = ?,
            image = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "sssi",
        $finalName,
        $finalEmail,
        $imageName,
        $admin_id
    );
}

$stmt->execute();

echo json_encode([
    "status" => "success",
    "message" => "Profile updated successfully"
]);

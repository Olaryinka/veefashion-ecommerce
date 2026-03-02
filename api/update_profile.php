<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

if (!isset($_SESSION['id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$user_id = (int) $_SESSION['id'];

/* FETCH CURRENT USER */
$stmt = $conn->prepare("
    SELECT fullname, email, phone, image, password
    FROM users WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

if (!$current) {
    echo json_encode(["status" => "error", "message" => "User not found"]);
    exit;
}

/* INPUTS (ALLOW EMPTY) */
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');

$oldPass  = $_POST['old_password'] ?? '';
$newPass  = $_POST['new_password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

$hasChanges = false;
$passwordChanged = false;

/* KEEP EXISTING VALUES BY DEFAULT */
$finalName  = $current['fullname'];
$finalEmail = $current['email'];
$finalPhone = $current['phone'];
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

if ($phone !== '' && $phone !== $current['phone']) {
    $finalPhone = $phone;
    $hasChanges = true;
}

/* IMAGE CHANGE */
if (!empty($_FILES['profile_image']['name'])) {
    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(["status" => "error", "message" => "Invalid image type"]);
        exit;
    }

    $imageName = time() . "_" . uniqid() . "." . $ext;
    move_uploaded_file(
        $_FILES['profile_image']['tmp_name'],
        $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/" . $imageName
    );

    $hasChanges = true;
}

/* PASSWORD CHANGE (OPTIONAL BUT STRICT) */
if ($oldPass || $newPass || $confirm) {

    if (!$oldPass || !$newPass || !$confirm) {
        echo json_encode(["status" => "error", "message" => "All password fields are required"]);
        exit;
    }

    if (!password_verify($oldPass, $current['password'])) {
        echo json_encode(["status" => "error", "message" => "Old password incorrect"]);
        exit;
    }

    if ($newPass !== $confirm) {
        echo json_encode(["status" => "error", "message" => "Passwords do not match"]);
        exit;
    }

    if (password_verify($newPass, $current['password'])) {
        echo json_encode(["status" => "error", "message" => "New password must be different"]);
        exit;
    }

    $newHash = password_hash($newPass, PASSWORD_DEFAULT);
    $passwordChanged = true;
    $hasChanges = true;
}

/* NO CHANGES AT ALL */
if (!$hasChanges) {
    echo json_encode(["status" => "error", "message" => "No changes detected"]);
    exit;
}

/* UPDATE QUERY */
if ($passwordChanged) {
    $stmt = $conn->prepare("
        UPDATE users SET
            fullname = ?, email = ?, phone = ?, image = ?, password = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "sssssi",
        $finalName,
        $finalEmail,
        $finalPhone,
        $imageName,
        $newHash,
        $user_id
    );
} else {
    $stmt = $conn->prepare("
        UPDATE users SET
            fullname = ?, email = ?, phone = ?, image = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "ssssi",
        $finalName,
        $finalEmail,
        $finalPhone,
        $imageName,
        $user_id
    );
}
$stmt->execute();
// update session image
if ($imageName !== $current['image']) {
    $_SESSION['image'] = $imageName;
}

echo json_encode([
    "status" => "success",
    "message" => "Account updated successfully"
]);

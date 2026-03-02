<?php
session_start();
$root = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
require_once("$root/config/db.php");
require_once("$root/config/function.php");

// Only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}
if (
    !isset($_SESSION['admin_id']) ||
    $_SESSION['role'] !== 'Admin'
) {
    echo json_encode([
        "status" => "error",
        "message" => "Unauthorized"
    ]);
    exit;
}


// Basic validation
$name  = trim($_POST['product_name'] ?? '');
$price = (float) ($_POST['price'] ?? 0);
$qty   = (int) ($_POST['qty'] ?? 0);
$status = trim($_POST['status'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($name === '' || $price <= 0 || $qty < 0 || $status === '') {
    echo json_encode([
        "status" => "error",
        "message" => "All required fields must be filled correctly"
    ]);
    exit;
}

/* IMAGE UPLOAD */
if (empty($_FILES['image']['name'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Product image is required"
    ]);
    exit;
}

$allowedExt = ['jpg', 'jpeg', 'png'];
$originalName = strtolower($_FILES['image']['name']);
$ext = pathinfo($originalName, PATHINFO_EXTENSION);

if (!in_array($ext, $allowedExt)) {
    echo json_encode([
        "status" => "error",
        "message" => "Only JPG, JPEG and PNG images allowed"
    ]);
    exit;
}

$imageName = time() . "_" . $originalName;
$uploadPath = "$root/assets/images/products-images/" . $imageName;

if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
    echo json_encode([
        "status" => "error",
        "message" => "Image upload failed"
    ]);
    exit;
}

/* INSERT PRODUCT */
$stmt = $conn->prepare("
    INSERT INTO products 
    (name, price, quantity, status, description, image, is_deleted)
    VALUES (?, ?, ?, ?, ?, ?, 0)
");

$stmt->bind_param(
    "sdisss",
    $name,
    $price,
    $qty,
    $status,
    $description,
    $imageName
);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Product added successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to add product"
    ]);
}

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
//   Collect POST data

$product_id  = (int) ($_POST['product_id'] ?? 0);
$name        = trim($_POST['product_name'] ?? '');
$price       = (float) ($_POST['price'] ?? 0);
$qty         = (int) ($_POST['qty'] ?? 0);
$status      = trim($_POST['status'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($product_id <= 0 || $name === '') {
    echo json_encode(["status" => "error", "message" => "Invalid product data"]);
    exit;
}

// Fetch existing product
$stmt = $conn->prepare("
    SELECT name, price, quantity, status, description, image
    FROM products
    WHERE id = ? AND is_deleted = 0
    LIMIT 1
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$original = $stmt->get_result()->fetch_assoc();

if (!$original) {
    echo json_encode(["status" => "error", "message" => "Product not found"]);
    exit;
}

// Detect changes
$hasChanges = false;

// Compare text fields
if (
    $name        !== $original['name'] ||
    $price       !=  $original['price'] ||
    $qty         !=  $original['quantity'] ||
    $status      !== $original['status'] ||
    $description !== $original['description']
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

// Handle image upload
$imageName = $original['image'];

if (!empty($_FILES['image']['name'])) {
    $allowed = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid image type"
        ]);
        exit;
    }

    $imageName = time() . "_" . uniqid() . "." . $ext;

    if (!move_uploaded_file(
        $_FILES['image']['tmp_name'],
        "$root/assets/images/products-images/" . $imageName
    )) {
        echo json_encode([
            "status" => "error",
            "message" => "Image upload failed"
        ]);
        exit;
    }
}

// Update product
$stmt = $conn->prepare("
    UPDATE products SET
        name = ?,
        price = ?,
        quantity = ?,
        status = ?,
        description = ?,
        image = ?
    WHERE id = ?
");

$stmt->bind_param(
    "sdisssi",
    $name,
    $price,
    $qty,
    $status,
    $description,
    $imageName,
    $product_id
);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Product updated successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update product"
    ]);
}

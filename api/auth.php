<?php
session_start();
$root_folder = $_SERVER['DOCUMENT_ROOT'] . "/veefashion";
define('BASE_URL', '/veefashion');
require_once("$root_folder/config/db.php");
require_once("$root_folder/config/function.php");
// $table = "users";
// $admin_table = "admin";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

//  SIGNUP LOGIC
//    SANITIZE INPUTS
if (isset($_POST['trigger_signup'])) {
    $firstname = $_POST['firstname'] ?? '';
    $lastname  = $_POST['lastname'] ?? '';
    $phone     = validate_phone($_POST['phone'] ?? '');
    $email     = strtolower(filter_email($_POST['email'] ?? ''));
    $password  = $_POST['password'] ?? '';

    if (!$firstname || !$lastname || !$phone || !$email || !$password) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }


    //    CHECK EMAIL EXISTS

    $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already exists"]);
        exit;
    }


    //    PASSWORD HASH

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $fullname = "$firstname $lastname";


    //    IMAGE UPLOAD (OPTIONAL)

    // $imageName = NULL;

    if (!empty($_FILES['image']['name'])) {

        $allowedExt = ['jpg', 'jpeg', 'png'];
        $originalName = strtolower($_FILES['image']['name']);
        $ext = pathinfo($originalName, PATHINFO_EXTENSION);

        if (!in_array($ext, $allowedExt)) {
            echo json_encode([
                "status" => "error",
                "message" => "Only JPG, JPEG and PNG images are allowed"
            ]);
            exit;
        }

        $imageName = time() . "_" . $originalName;
        $uploadPath = "../assets/images/profile_upload/" . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            echo json_encode([
                "status" => "error",
                "message" => "Image upload failed"
            ]);
            exit;
        }
    }


    //    INSERT USER

    $stmt = $conn->prepare("
        INSERT INTO users (fullname, email, phone, password, image, role, status)
        VALUES (?, ?, ?, ?, ?, 'User', 'Active')
    ");

    $stmt->bind_param("sssss", $fullname,  $email,  $phone,  $hashedPassword, $imageName);
    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success",
            "message" => "Account created successfully"
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Signup failed"
        ]);
    }
}
// User Login Logic
if (isset($_POST['trigger_signin'])) {
    $email    =  strtolower(filter_email($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(["status" => "error", "message" => "Email and password required"]);
        exit;
    }

    /* CHECK USER */
    $stmt = $conn->prepare("SELECT id, fullname, email, phone, password, image, role, status FROM users  WHERE email = ? AND is_deleted = 0 LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
        exit;
    }

    $user = $result->fetch_assoc();

    /* CHECK STATUS */
    if ($user['status'] !== 'Active') {
        echo json_encode(["status" => "error", "message" => "Your account has been suspended. Please contact support."]);
        exit;
    }

    /* VERIFY PASSWORD */
    if (!password_verify($password, $user['password'])) {
        echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
        exit;
    }

    // Setup session to track the user
    $_SESSION['id']   = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['image'] = $user['image'];
    $_SESSION['role'] = $user['role'];
    echo json_encode([
        "status" => "success",
        "message" => "Welcome back {$user['fullname']}, Redirecting...",
        "redirect" => BASE_URL . "/index.php"
    ]);
}
// Admin Login Logic
if(isset($_POST['trigger_admin_signup'])){
    
    $email    = strtolower(filter_email($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    
    if (!$email || !$password) {
        echo json_encode(["status" => "error", "message" => "Email and password required"]);
        exit;
    }
    
    /* CHECK ADMIN */
    $stmt = $conn->prepare(" SELECT id, fullname, email, password, image, role, status FROM admins WHERE email = ? AND is_deleted = 0 LIMIT 1 " );   
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Invalid admin credentials"]);
        exit;
    }
    $admin = $result->fetch_assoc(); 
    /* CHECK STATUS */
    if ($admin['status'] !== 'Active') {
        echo json_encode(["status" => "error", "message" => "Admin account suspended"]);
        exit;
    }
    
    /* VERIFY PASSWORD */
    if (!password_verify($password, $admin['password'])) {
        echo json_encode(["status" => "error", "message" => "Invalid admin credentials"]);
        exit;
    }
    
    /* SET ADMIN SESSION */
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['fullname'] = $admin['fullname'];
    $_SESSION['email'] = $admin['email'];
    $_SESSION['image'] = $admin['image'];
    $_SESSION['role'] = 'Admin';
    $_SESSION['admin_role'] = $admin['role']; // Super / Regular
    
    echo json_encode([
        "status" => "success",
        "message" => "Welcome back {$admin['fullname']}",
        "redirect" => "../admin/dashboard/admin.php"
    ]);
}

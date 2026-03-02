<?php
require_once(__DIR__ . "/includes/dash_header.php");

if ($_SESSION['admin_role'] !== 'Super') {
    header("Location: /veefashion/admin/dashboard/admin.php?error=unauthorized");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT id, fullname, email, phone, image, role, status, created_at
    FROM users
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: users.php");
    exit;
}

$user = $result->fetch_assoc();

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/";
if (!empty($user['image']) && file_exists($uploadDir . $user['image'])) {
    $avatar = BASE_URL . "/assets/images/profile_upload/" . $user['image'];
} else {
    $avatar = BASE_URL . "/assets/images/avatar.jpg";
}

?>

<div class="edit_user_container">

    <div class="edit_user_wrapper">
        <h2 class="edit_user_heading">Edit User Profile</h2>

        <a href="<?= BASE_URL ?>/admin/dashboard/users.php" class="back_btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Users
        </a>

    </div>

    <form class="edit_user_form" enctype="multipart/form-data">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

        <!-- LEFT: USER PICTURE -->
        <div class="edit_user_left">
            <img id="userPreview" src="<?= $avatar?>"
                onerror="this.src='<?= BASE_URL ?>/assets/images/avatar.jpg'" class="edit_user_avatar">

            <label for="upload" class="upload_label">
                <i class="fa-solid fa-camera"></i> Change Photo
            </label>
            <input type="file" name="image" id="upload" class="upload_input" accept=".jpg,.jpeg,.png">
        </div>

        <!-- RIGHT: USER DETAILS -->
        <div class="edit_user_right">

            <div class="input_group">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?= esc($user['fullname']) ?>">
            </div>

            <div class="input_group">
                <label>Email</label>
                <input type="email" name="email" value="<?= esc($user['email']) ?>">
            </div>

            <div class="input_group">
                <label>Phone</label>
                <input type="text" name="phone" value="<?= esc($user['phone']) ?>">
            </div>

            <div class="input_group">
                <label>Role</label>
                <select name="role">
                    <option value="Admin" <?= $user['role'] === 'Admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="User" <?= $user['role'] === 'User' ? 'selected' : '' ?>>User</option>
                </select>
            </div>

            <div class="input_group">
                <label>Status</label>
                <select name="status">
                    <option value="Active" <?= $user['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Suspended" <?= $user['status'] === 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>

            <div class="input_group">
                <label class="joined-date">Date Joined</label>
                <p class="date_joined"><?= date("M d, Y", strtotime($user['created_at'])) ?></p>
            </div>

            <button type="submit" class="save_user_btn">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>

        </div>

    </form>

</div>
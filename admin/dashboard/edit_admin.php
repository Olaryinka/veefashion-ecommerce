<?php
require_once(__DIR__ . "/includes/dash_header.php");

if ($_SESSION['admin_role'] !== 'Super') {
    header("Location: /veefashion/admin/dashboard/admin.php?error=unauthorized");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admins.php");
    exit;
}

$admin_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT id, fullname, email, image, role, status, created_at
    FROM admins
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admins.php");
    exit;
}

$admin = $result->fetch_assoc();

$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/";
if (!empty($admin['image']) && file_exists($uploadDir . $admin['image'])) {
    $avatar = BASE_URL . "/assets/images/profile_upload/" . $admin['image'];
} else {
    $avatar = BASE_URL . "/assets/images/avatar.jpg";
}

?>

<div class="edit_user_container">

    <div class="edit_user_wrapper">
        <h2 class="edit_user_heading">Edit Admin Profile</h2>

        <a href="<?= BASE_URL ?>/admin/dashboard/admins.php" class="back_btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Admins
        </a>

    </div>

    <form  id="edit_admin_form" enctype="multipart/form-data">
        <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">

        <!-- LEFT: ADMIN PICTURE -->
        <div class="edit_user_left">
            <img id="userPreview" src="<?= $avatar ?>"
                onerror="this.src='<?= BASE_URL ?>/assets/images/avatar.jpg'" class="edit_user_avatar">

            <label for="upload" class="upload_label">
                <i class="fa-solid fa-camera"></i> Change Photo
            </label>
            <input type="file" name="image" id="upload" class="upload_input" accept=".jpg,.jpeg,.png">
        </div>

        <!-- RIGHT: ADMIN DETAILS -->
        <div class="edit_user_right">

            <div class="input_group">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?= esc($admin['fullname']) ?>">
            </div>

            <div class="input_group">
                <label>Email</label>
                <input type="email" name="email" value="<?= esc($admin['email']) ?>">
            </div>

            <div class="input_group">
                <label>Role</label>
                <select name="role">
                    <option value="Super" <?= $admin['role'] === 'Super' ? 'selected' : '' ?>>Super</option>
                    <option value="Regular" <?= $admin['role'] === 'Regular' ? 'selected' : '' ?>>Regular</option>
                </select>
            </div>

            <div class="input_group">
                <label>Status</label>
                <select name="status">
                    <option value="Active" <?= $admin['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Suspended" <?= $admin['status'] === 'Suspended' ? 'selected' : '' ?>>Suspended</option>
                </select>
            </div>

            <div class="input_group">
                <label class="joined-date">Date Joined</label>
                <p class="date_joined"><?= date("M d, Y", strtotime($admin['created_at'])) ?></p>
            </div>

            <button type="submit" class="save_user_btn">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>

        </div>

    </form>

</div>
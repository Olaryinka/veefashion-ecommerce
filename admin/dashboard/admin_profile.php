<?php
require_once(__DIR__ . "/includes/dash_header.php");

if ($_SESSION['admin_role'] !== 'Super') {
    header("Location: /veefashion/admin/dashboard/admin.php?error=unauthorized");
    exit;
}
$admin_id = $_SESSION['admin_id'];

$stmt = $conn->prepare("
    SELECT fullname, email, role, status, image
    FROM admins
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

$adminImage = (!empty($admin['image']) && file_exists(
    $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/" . $admin['image']
))
    ? BASE_URL . "/assets/images/profile_upload/" . $admin['image']
    : BASE_URL . "/assets/images/avatar.jpg";
?>

<div class="admin_profile_container">
    <h2 class="profile_heading">
        <i class="fa-solid fa-user-shield"></i> Admin Profile
    </h2>

    <div class="profile_wrapper">

        <!-- LEFT SIDE — PROFILE PHOTO -->
        <div class="profile_left">

            <img src="<?= $adminImage ?>" class="profile_avatar" id="adminPreview">
            <!-- Trigger button -->
            <button type="button" class="change_photo_btn" id="changePhotoBtn">
                <i class="fa-solid fa-camera"></i> Change Photo
            </button>
        </div>


        <!-- RIGHT SIDE — FORM INFO -->
        <div class="profile_right">

            <form id="adminProfileForm" class="profile_form" enctype="multipart/form-data">
                <input type="hidden" name="admin_id" value="<?= $admin_id ?>">
                <!-- Hidden file input -->
                <input type="file" id="adminImageUpload" name="image" accept=".jpg, .jpeg, .png" hidden>


                <div class="input_group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" placeholder="<?= esc($admin['fullname']) ?>">
                </div>

                <div class="input_group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="<?= esc($admin['email']) ?>">
                </div>

                <h3 class="security_heading">Security Settings</h3>

                <div class="input_group">
                    <label>Old Password</label>
                    <input type="password" name="old_password" placeholder="Enter old password">
                </div>

                <div class="input_group">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="Enter new password">
                </div>

                <div class="input_group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Confirm new password">
                </div>

                <button class="update_profile_btn">Update Profile</button>

            </form>
        </div>

    </div>

</div>
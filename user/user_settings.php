<?php
require_once("./includes/user_header.php");


$user_id = $_SESSION['id'];

$stmt = $conn->prepare("
    SELECT fullname, email, phone, image, password
    FROM users
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("User not found");
}
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/";
if (!empty($_SESSION['image']) && file_exists($uploadDir . $_SESSION['image'])) {
    $userImage = BASE_URL . "/assets/images/profile_upload/" . $_SESSION['image'];
} else {
    $userImage = BASE_URL . "/assets/images/avatar.jpg";
}
?>

<div class="user_settings_container">

    <h2 class="user_settings_title"><i class="fa-solid fa-gear"></i> Account Settings</h2>
    <p class="user_settings_sub">Manage your personal information and profile details</p>

    <div class="user_settings_grid">

        <!-- LEFT SIDE — PROFILE PHOTO -->
        <div class="user_settings_left">
            <img src="<?= $userImage ?>" id="userAvatarPreview" class="user_avatar">
            <button class="user_change_photo_btn" id="changeUserPhotoBtn">
                <i class="fa-solid fa-camera"></i> Change Photo
            </button>
        </div>

        <!-- RIGHT SIDE — FORM -->
        <form id="userSettingsForm" method="POST" enctype="multipart/form-data" class="user_settings_form">
            <input type="file" name="profile_image" id="userAvatarInput" accept=".jpg,.jpeg,.png" hidden>

            <div class="settings_group">
                <label>Full Name</label>
                <input type="text" name="fullname" placeholder="<?= $user['fullname'] ?>">
            </div>

            <div class="settings_group">
                <label>Email</label>
                <input type="email" name="email" placeholder="<?= $user['email'] ?>">
            </div>

            <div class="settings_group">
                <label>Phone Number</label>
                <input type="text" name="phone" placeholder="<?= $user['phone'] ?>">
            </div>

            <div class="settings_group">
                <label>Old Password</label>
                <input type="password" name="old_password" placeholder="Enter your current password">
            </div>
            <div class="settings_group">
                <label>New Password</label>
                <input type="password" name="new_password" placeholder="Enter your new password">
            </div>
            <div class="settings_group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm your new password">
            </div>

            <button type="submit" class="user_save_btn">
                <i class="fa-solid fa-check"></i> Save Changes
            </button>

        </form>

    </div>

</div>
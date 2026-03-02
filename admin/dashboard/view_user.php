<?php
require_once(__DIR__ . "/includes/dash_header.php");

if ($_SESSION['admin_role'] !== 'Super') {
    header("Location: /veefashion/admin/dashboard/admin.php?error=unauthorized");
    exit;
}

// 1. Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: users.php");
    exit;
}

$user_id = (int) $_GET['id'];

// 2. Fetch user (ONLY if NOT deleted)
$stmt = $conn->prepare("
    SELECT id, fullname, email, phone, image, role, status, created_at
    FROM users
    WHERE id = ? AND is_deleted = 0
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // User does not exist OR was deleted
    header("Location: users.php");
    exit;
}

$user = $result->fetch_assoc();

// 3. Profile image fallback
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/";
if (!empty($user['image']) && file_exists($uploadDir . $user['image'])) {
    $userImage = BASE_URL . "/assets/images/profile_upload/" . $user['image'];
} else {
    $userImage = BASE_URL . "/assets/images/avatar.jpg";
}
?>


<div class="user_profile_container">
    <!-- PAGE TITLE -->
    <h2 class="user_profile_heading"><i class="fa-solid fa-user"></i> User Profile</h2>

    <div class="user_profile_card">

        <!-- LEFT: USER PHOTO & BASIC INFO -->
        <div class="profile_left">

            <img src="<?= $userImage ?>" class="profile_avatar">

            <h3 class="profile_name"><?= esc($user['fullname']) ?></h3>
            <p class="profile_email"><?= esc($user['email']) ?></p>

            <span class="profile_role user_role"><?= esc($user['role']) ?></span>
            <span class="profile_status <?= $user['status'] === 'Active' ? 'active_status' : 'suspended_status' ?>">
                <?= esc($user['status']) ?></span>
            <p class="profile_joined"><?= date("M d, Y", strtotime($user['created_at'])) ?></p>
        </div>

        <!-- RIGHT: USER DETAILS -->
        <div class="profile_right">

            <div class="profile_info_row">
                <span class="label">Full Name:</span>
                <span class="value"><?= esc($user['fullname']) ?></span>
            </div>

            <div class="profile_info_row">
                <span class="label">Email:</span>
                <span class="value"><?= esc($user['email']) ?></span>
            </div>

            <div class="profile_info_row">
                <span class="label">Phone:</span>
                <span class="value"><?= esc($user['phone']) ?></span>
            </div>

            <div class="profile_info_row">
                <span class="label">Role:</span>
                <span class="value"><?= esc($user['role']) ?></span>
            </div>

            <div class="profile_info_row">
                <span class="label">Status:</span>
                <span class="value"><?= esc($user['status']) ?></span>
            </div>

            <div class="profile_info_row">
                <span class="label">Date Joined:</span>
                <span class="value"><?= date("M d, Y", strtotime($user['created_at'])) ?></span>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="profile_actions">
                <a href="<?= BASE_URL ?>/admin/dashboard/edit_user.php?id=<?= $user['id'] ?>" class="edit_btn">
                    <i class="fa-solid fa-pen-to-square"></i> Edit User
                </a>

                <button class="user_suspend_btn" data-id="<?= $user['id'] ?>">
                    <i class="fa-solid fa-ban"></i> Suspend
                </button>

                <button class="user_delete_btn" data-id="<?= $user['id'] ?>">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>


                <a href="<?= BASE_URL ?>/admin/dashboard/users.php" class="back_btn">
                    <i class="fa-solid fa-arrow-left"></i> Back to Users
                </a>

            </div>

        </div>

    </div>

</div>
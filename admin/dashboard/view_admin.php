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

$admin_id = (int) $_GET['id'];

// 2. Fetch admin (ONLY if NOT deleted)
$stmt = $conn->prepare("
    SELECT id, fullname, email, image, role, status, created_at
    FROM admins
    WHERE id = ? AND is_deleted = 0
    LIMIT 1
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Admin does not exist OR was deleted
    header("Location: admins.php");
    exit;
}

$admin = $result->fetch_assoc();

// 3. Profile image fallback
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/";
if (!empty($admin['image']) && file_exists($uploadDir . $admin['image'])) {
    $adminImage = BASE_URL . "/assets/images/profile_upload/" . $admin['image'];
} else {
    $adminImage = BASE_URL . "/assets/images/avatar.jpg";
}
?>


<div class="user_profile_container">
    <!-- PAGE TITLE -->
    <h2 class="user_profile_heading"><i class="fa-solid fa-user"></i> Admin Profile</h2>

    <div class="user_profile_card">

        <!-- LEFT: USER PHOTO & BASIC INFO -->
        <div class="profile_left">

            <img src="<?= $adminImage ?>" class="profile_avatar">

            <h3 class="profile_name"><?= esc($admin['fullname']) ?></h3>
            <p class="profile_email"><?= esc($admin['email']) ?></p>

            <span class="profile_role user_role"><?= esc($admin['role']) ?></span>
            <span class="profile_status <?= $admin['status'] === 'Active' ? 'active_status' : 'suspended_status' ?>">
                <?= esc($admin['status']) ?></span>
            <p class="profile_joined"><?= date("M d, Y", strtotime($admin['created_at'])) ?></p>
        </div>

        <!-- RIGHT: admin DETAILS -->
        <div class="profile_right">

            <div class="profile_info_row">
                <span class="label">Full Name:</span>
                <span class="value"><?= esc($admin['fullname']) ?></span>
            </div>

            <div class="profile_info_row">
                <span class="label">Email:</span>
                <span class="value"><?= esc($admin['email']) ?></span>
            </div>

            <div class="profile_info_row">
                <span class="label">Role:</span>
                <span class="value"><?= esc($admin['role']) ?></span>
            </div>

            <div class="profile_info_row">
                <span class="label">Status:</span>
                <span class="value"><?= esc($admin['status']) ?></span>
            </div>

            <div class="profile_info_row">
                <span class="label">Date Joined:</span>
                <span class="value"><?= date("M d, Y", strtotime($admin['created_at'])) ?></span>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="profile_actions">
                <a href="<?= BASE_URL ?>/admin/dashboard/edit_admin.php?id=<?= $admin['id'] ?>" class="edit_btn">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Admin
                </a>

                <button class="suspend_btn" data-id="<?= $admin['id'] ?>">
                    <i class="fa-solid fa-ban"></i> Suspend
                </button>

                <button class="delete_btn" data-id="<?= $admin['id'] ?>">
                    <i class="fa-solid fa-trash"></i> Delete
                </button>


                <a href="<?= BASE_URL ?>/admin/dashboard/admins.php" class="back_btn">
                    <i class="fa-solid fa-arrow-left"></i> Back to Admins
                </a>

            </div>

        </div>

    </div>

</div>
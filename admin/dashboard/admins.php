<?php
require_once(__DIR__ . "/includes/dash_header.php");
if ($_SESSION['admin_role'] !== 'Super') {
    header("Location: /veefashion/admin/dashboard/admin.php?error=unauthorized");
    exit;
}

$admins = $conn->query("SELECT * FROM admins WHERE is_deleted = 0 ORDER BY created_at DESC");
?>

<section class="orders_section">

    <div class="orders_header">
        <h4 class="table_heading">
            <i class="fa-solid fa-users"></i> All Admins
        </h4>
    </div>

    <div class="table_wrapper">
        <table class="orders_table">

            <thead>
                <tr>
                    <th>SN</th>
                    <th>Admin ID</th>
                    <th>Admin</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Date Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($admins && $admins->num_rows > 0): ?>
                    <?php $sn = 1;
                    while ($admin = $admins->fetch_assoc()): ?>
                        <tr>

                            <!-- SN -->
                            <td><?= $sn++ ?></td>

                            <!-- ADMIN ID -->
                            <td>#<?= $admin['id'] ?></td>

                            <!-- ADMIN IMAGE -->
                            <td>
                                <?php
                                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/";
                                if (!empty($admin['image']) && file_exists($uploadDir . $admin['image'])) {
                                    $avatar = BASE_URL . "/assets/images/profile_upload/" . $admin['image'];
                                } else {
                                    $avatar = BASE_URL . "/assets/images/avatar.jpg";
                                }
                                ?>
                                <img src="<?= $avatar ?>" class="user_table_avatar">
                            </td>

                            <!-- FULL NAME -->
                            <td><?= esc($admin['fullname']) ?></td>

                            <!-- EMAIL -->
                            <td><?= esc($admin['email']) ?></td>


                            <!-- ROLE -->
                            <td>
                                <span class="badge role_user">
                                    <?= $admin['role'] ?>
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td>
                                <span class="badge <?= $admin['status'] === 'Active' ? 'active' : 'Suspended' ?>">
                                    <?= $admin['status'] ?>
                                </span>
                            </td>

                            <!-- DATE JOINED -->
                            <td><?= date("M d, Y", strtotime($admin['created_at'])) ?></td>

                            <!-- ACTIONS -->
                            <td class="action_icons">
                                <a href="<?= BASE_URL ?>/admin/dashboard/view_admin.php?id=<?= $admin['id'] ?>"
                                    class="order_view_btn">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;">
                            No Admins found
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>
    </div>

</section>
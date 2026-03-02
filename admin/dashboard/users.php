<?php
require_once(__DIR__ . "/includes/dash_header.php");
if ($_SESSION['admin_role'] !== 'Super') {
    header("Location: /veefashion/admin/dashboard/admin.php?error=unauthorized");
    exit;
}

$users = $conn->query("SELECT * FROM users WHERE is_deleted = 0 ORDER BY created_at DESC");
?>

<section class="orders_section">

    <div class="orders_header">
        <h4 class="table_heading">
            <i class="fa-solid fa-users"></i> All Users
        </h4>
    </div>

    <div class="table_wrapper">
        <table class="orders_table">

            <thead>
                <tr>
                    <th>SN</th>
                    <th>User ID</th>
                    <th>User</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Date Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if ($users && $users->num_rows > 0): ?>
                    <?php $sn = 1;
                    while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <!-- SN -->
                            <td><?= $sn++ ?></td>

                            <!-- USER ID -->
                            <td>#<?= $user['id'] ?></td>

                            <!-- USER IMAGE -->
                            <td>
                                <?php
                                $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/images/profile_upload/";
                                if (!empty($user['image']) && file_exists($uploadDir . $user['image'])) {
                                    $avatar = BASE_URL . "/assets/images/profile_upload/" . $user['image'];
                                } else {
                                    $avatar = BASE_URL . "/assets/images/avatar.jpg";
                                }
                                ?>
                                <img src="<?= $avatar ?>" class="user_table_avatar">
                            </td>

                            <!-- FULL NAME -->
                            <td><?= esc($user['fullname']) ?></td>

                            <!-- EMAIL -->
                            <td><?= esc($user['email']) ?></td>

                            <!-- PHONE -->
                            <td><?= esc($user['phone']) ?></td>

                            <!-- ROLE -->
                            <td>
                                <span class="badge role_user">
                                    <?= $user['role'] ?>
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td>
                                <span class="badge <?= $user['status'] === 'Active' ? 'Active' : 'Suspended' ?>">
                                    <?= $user['status'] ?>
                                </span>
                            </td>

                            <!-- DATE JOINED -->
                            <td><?= date("M d, Y", strtotime($user['created_at'])) ?></td>

                            <!-- ACTIONS -->
                            <td class="action_icons">
                                <a href="<?= BASE_URL ?>/admin/dashboard/view_user.php?id=<?= $user['id'] ?>"
                                    class="order_view_btn">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;">
                            No users found
                        </td>
                    </tr>
                <?php endif; ?>

            </tbody>

        </table>
    </div>

</section>
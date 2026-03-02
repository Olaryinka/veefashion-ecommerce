<?php
require_once(__DIR__ . "/includes/dash_header.php");

?>


<div class="products_container">

    <div class="products_topbar">
        <h2 class="dash_page_title"><i class="fa-solid fa-envelope"></i> Enquiries</h2>
    </div>

    <?php
    $result = $conn->query("SELECT * FROM enquiries ORDER BY created_at DESC");
    ?>
    <div class="products_table_wrapper">
        <table class="products_table">
            <thead>
                <tr>
                    <th>SN</th>
                    <th>Enquiry ID</th>
                    <th>Sender</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>Date Sent</th>
                </tr>
            </thead>

            <tbody>
                <?php $sn = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $sn++ ?></td>
                        <td>#<?= esc($row['enquiry_code']) ?></td>
                        <td><?= esc($row['fullname']) ?></td>
                        <td><?= esc($row['email']) ?></td>
                        <td><?= substr(esc($row['message']), 0, 30) ?></td>
                        <td>
                            <?php if ($row['status'] === 'New'): ?>
                                <span class="status msg_new">New</span>
                            <?php elseif ($row['status'] === 'Read'): ?>
                                <span class="status msg_read">Read</span>
                            <?php elseif ($row['status'] === 'Replied'): ?>
                                <span class="status msg_replied">Replied</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="view_enquiry.php?id=<?= $row['id'] ?>" class="view_enquiry_btn">View</a>
                        </td>
                        <td><?= date("d-m-Y", strtotime($row['created_at'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
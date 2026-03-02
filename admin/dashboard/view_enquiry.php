<?php
require_once(__DIR__ . "/includes/dash_header.php");


$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid enquiry");
}

/* Mark enquiry as READ */
$conn->query("  UPDATE enquiries 
    SET status = 'Read' 
    WHERE id = $id 
    AND status = 'New'");

/* Fetch enquiry */
$stmt = $conn->prepare("SELECT * FROM enquiries WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$enquiry = $stmt->get_result()->fetch_assoc();

if (!$enquiry) {
    die("Enquiry not found");
}
?>


<h2 class="enquiry_details">Enquiry Details</h2>

<p class="enquiry_details-p"><strong>From:</strong> <?= esc($enquiry['fullname']) ?></p>
<p class="enquiry_details-p"><strong>Email:</strong> <?= esc($enquiry['email']) ?></p>
<p class="enquiry_details-p"><strong>Phone:</strong> <?= esc($enquiry['phone']) ?></p>

<hr class="horizontal-line">

<p class="enquiry_message-heading"><strong>Message:</strong></p>
<p class="enquiry_details-mssg"><?= nl2br(esc($enquiry['message'])) ?></p>

<hr class="horizontal-line">

<!-- Reply Form -->
 
<form id="replyForm" class="replyForm">
    <input type="hidden" name="enquiry_id" value="<?= $enquiry['id'] ?>">
    <input type="hidden" name="email" value="<?= $enquiry['email'] ?>">

    <div class="reply_heading">
        <h3>Reply to Enquiry</h3>
        <a href="<?= BASE_URL ?>/admin/dashboard/enquiries.php" class="back_btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Enquiry
        </a>
    </div>

    <textarea class="reply_enquiry" name="reply_message" rows="10"  placeholder="Type your reply here..."></textarea>

    <button type="submit" class="reply-btn">
        Send Reply
    </button>
</form>

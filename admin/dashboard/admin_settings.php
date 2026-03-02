<?php
require_once(__DIR__ . "/includes/dash_header.php");

if ($_SESSION['admin_role'] !== 'Super') {
    header("Location: /veefashion/admin/dashboard/admin.php?error=unauthorized");
    exit;
}
$stmt = $conn->prepare("SELECT * FROM settings LIMIT 1");
$stmt->execute();
$settings = $stmt->get_result()->fetch_assoc();

$logoFile = $settings['logo'] ?? '';
$logoPath = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/icon/" . $logoFile;
if (!empty($logoFile) && file_exists($logoPath)) {
    $logoUrl = BASE_URL . "/assets/icon/" . $logoFile;
} else {
    $logoUrl = BASE_URL . "/assets/icon/logo.png"; // fallback
}

?>

<div class="settings_container">

    <h2 class="settings_title"><i class="fa-solid fa-gear"></i> Site Settings</h2>
    <p class="settings_sub">Manage your website configuration and branding</p>

    <form id="settingsForm" method="POST" enctype="multipart/form-data" class="settings_form">

        <!-- SITE NAME -->
        <div class="settings_group">
            <label for="siteName">Site Name</label>
            <input type="text" placeholder="<?= esc($settings['site_name']) ?>" id="siteName" name="site_name">
        </div>

        <!-- CONTACT EMAIL -->
        <div class="settings_group">
            <label for="contactEmail">Contact Email</label>
            <input type="email" placeholder="<?= esc($settings['contact_email']) ?>" id="contactEmail" name="contact_email">
        </div>

        <!-- CONTACT PHONE -->
        <div class="settings_group">
            <label for="contactPhone">Contact Phone</label>
            <input type="tel" placeholder="<?= esc($settings['contact_phone']) ?>" id="contactPhone" name="contact_phone">
        </div>

        <!-- WELCOME MESSAGE -->
        <div class="settings_group">
            <label for="welcomeText">Homepage Welcome Message</label>
            <textarea id="welcomeText" name="welcome_text" rows="4">
                <?= esc($settings['welcome_text']) ?>
            </textarea>
        </div>

        <!-- LOGO UPLOAD -->
        <div class="settings_group">
            <label>Website Logo</label>

            <div class="settings_logo_preview">
                <img src="<?= esc($logoUrl) ?>" id="logoPreview" class="logo_img">
            </div>

            <input type="file" id="logoUpload" name="logo" accept=".jpg,.jpeg,.png" hidden>

            <button type="button" class="upload_logo_btn" id="changeLogoBtn">
                <i class="fa-solid fa-camera"></i> Change Logo
            </button>
        </div>

        <!-- SAVE BUTTON -->
        <button type="submit" class="settings_save_btn">
            <i class="fa-solid fa-check"></i> Save Settings
        </button>

    </form>
</div>
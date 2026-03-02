<?php
$stmt = $conn->prepare("SELECT * FROM settings LIMIT 1");
$stmt->execute();
$siteSettings = $stmt->get_result()->fetch_assoc();

/* Safe fallbacks */
$SITE_NAME   = $siteSettings['site_name'] ?? 'Vee - Fashion House';
$WELCOME_TXT = $siteSettings['welcome_text'] ?? 'Welcome to Vee-Fashion House! ';
$CONTACT_EMAIL = $siteSettings['contact_email'] ?? 'support@veefashion.com';
$CONTACT_PHONE = $siteSettings['contact_phone'] ?? '+234 800 000 0000';   

$logoFile = $siteSettings['logo'] ?? '';
$logoPath = $_SERVER['DOCUMENT_ROOT'] . "/veefashion/assets/icon/" . $logoFile;
if (!empty($logoFile) && file_exists($logoPath)) {
    $SITE_LOGO = BASE_URL . "/assets/icon/" . $logoFile;
} else {
    $SITE_LOGO = BASE_URL . "/assets/icon/logo.png"; // fallback
}

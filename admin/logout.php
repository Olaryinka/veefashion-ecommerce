<?php
session_start();
/* Destroy all session data */
$_SESSION = [];
session_unset();
session_destroy();
/* Redirect to admin login */
header("Location: /veefashion/admin/signin.php?logout=success");
exit;

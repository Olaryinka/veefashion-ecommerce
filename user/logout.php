<?php
session_start();
/* Destroy all session data */
$_SESSION = [];
session_unset();
session_destroy();
/* Redirect to user login */
header("Location: /veefashion/signin.php?logout=success");
exit;

<?php
function filter_email($email){
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    return strtolower($email); // optional but recommended
}
function filter_int($value){
    return filter_var($value, FILTER_VALIDATE_INT);
}
function esc($esc){
      if ($esc === null) {
        return '';
    }
    return htmlspecialchars(strip_tags(trim($esc)), ENT_QUOTES, 'UTF-8');
}

function validate_phone($phone) {
    $phone = preg_replace("/[^0-9]/", "", $phone); // keep only digits

    if (strlen($phone) < 10 || strlen($phone) > 15) {
        return false;
    }
    return $phone;
}


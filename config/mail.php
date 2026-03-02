<?php
$root_folder = dirname(__DIR__);
require_once("$root_folder/config/env.php");
require_once("$root_folder/vendor/autoload.php");

use PHPMailer\PHPMailer\PHPMailer;

function sendMail($toEmail, $toName, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        // Mailtrap SMTP settings
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAIL_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAIL_USER']; // Username
        $mail->Password   = $_ENV['MAIL_PASS'];   //  password
        $mail->SMTPSecure = 'false';
        $mail->Port       = $_ENV['MAIL_PORT'];

        // Email headers
        $mail->setFrom('talk2coder.o@gmail.com', 'Vee Fashion House');
        $mail->addAddress($toEmail, $toName);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
        
    } catch (Exception $e) {
        return false;
    }
    // catch (Exception $e) {
    //     echo json_encode([
    //         "status" => "error",
    //         "message" => $mail->ErrorInfo
    //     ]);
    //     exit;
    // }

}

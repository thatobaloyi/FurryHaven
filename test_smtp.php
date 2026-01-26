<?php

error_reporting(E_ALL);
ini_set('display_errors',1);
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


    // EDIT THESE BEFORE RUNNING
    $smtpHost = 'smtp.gmail.com';
    $smtpUser = 'furryhavendonations@gmail.com';
    $smtpPass = 'oanj gsxj qumr voni';
    $smtpPort = 587;
    $smtpSecure = 'tls';
    $fromAddress = 'furryhavenanalytics@gmail.com';
    $fromName = 'FurryHaven Analytics';


$to = 'karabontsie2405@gmail.com';
$subject = 'SMTP Test from FurryHaven';
$body = "This is a test email sent from the server at " . date('Y-m-d H:i:s') . ".";

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Port = $smtpPort;

    // debug output to browser and error_log for troubleshooting
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        error_log("PHPMailer debug [$level]: $str");
        echo nl2br(htmlspecialchars($str)) . "<br>";
    };

    $mail->setFrom($fromAddress, $fromName);
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->send();
    echo "<p style='color:green;'>OK: message sent to {$to}</p>";
} catch (Exception $e) {
    echo "<p style='color:red;'>FAILED: " . htmlspecialchars($e->getMessage()) . "</p>";
    if (property_exists($mail, 'ErrorInfo')) {
        echo "<pre>" . htmlspecialchars($mail->ErrorInfo) . "</pre>";
        error_log("PHPMailer ErrorInfo: " . $mail->ErrorInfo);
    }
    error_log("PHPMailer Exception: " . $e->getMessage());
}

?>
<?php
require 'vendor/autoload.php';
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com'; // e.g., smtp.gmail.com
$mail->SMTPAuth = true;
$mail->Username = 'furryhavendonations@gmail.com';
$mail->Password = 'oanj gsxj qumr voni';
$mail->SMTPSecure = 'tls';
$mail->Port = 587;
$mail->setFrom('furryhavendonations@gmail.com', 'Test');
$mail->addAddress('karabontsie2405@gmail.com'); // <-- put your real email here
$mail->Subject = "Test Email";
$mail->Body = "This is a test.";
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Sent!";
}
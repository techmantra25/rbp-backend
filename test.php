<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.office365.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'coziclubsupport@luxcozi.com';
    $mail->Password = 'C0zi!1234';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('coziclubsupport@luxcozi.com', 'Your Name');
    $mail->addAddress('priya.m@techmantra.co');
    $mail->Subject = 'Test Email';
    $mail->Body = 'This is a test email.';

    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
}
?>

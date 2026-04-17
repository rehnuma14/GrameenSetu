<?php
ob_start(); // ✅ START BUFFER

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0); // keep OFF for clean JSON

require_once 'config.php';
require_once '../vendor/autoload.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rehnuma.14.11@gmail.com';
    $mail->Password   = 'kski jcxi kofg yndl';
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('rehnuma.14.11@gmail.com', 'Grameen Setu Test');
    $mail->addAddress('rehnuma.14.11@gmail.com', 'Test User'); // Send to yourself for testing

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email - Grameen Setu';
    $mail->Body    = '<h2>Test Email</h2><p>This is a test email to verify Gmail SMTP configuration.</p>';
    $mail->AltBody = 'This is a test email to verify Gmail SMTP configuration.';

    $mail->send();
    echo 'Test email sent successfully!';
} catch (Exception $e) {
    echo "Email failed: " . $mail->ErrorInfo;
}
ob_end_clean();
echo json_encode($response);
exit;
?>
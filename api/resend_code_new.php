<?php
ob_start(); // ✅ START BUFFER

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0); // keep OFF for clean JSON

require_once 'config.php';
header('Content-Type: application/json');

$user_id = $_POST['user_id'] ?? '';
if (empty($user_id)) {
    echo json_encode(['status' => 'error', 'message' => 'User ID required']);
    exit;
}

$user_id = intval($user_id);
$stmt = $conn->prepare("SELECT email, full_name FROM users WHERE id = ? AND is_verified = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'User not found or already verified']);
    exit;
}

$new_code = sprintf("%06d", mt_rand(0, 999999));
$stmt = $conn->prepare("UPDATE users SET verification_code = ? WHERE id = ?");
$stmt->bind_param("si", $new_code, $user_id);
$stmt->execute();
$stmt->close();

$email_sent = false;
try {
    require_once '../vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rehnuma.14.11@gmail.com';
    $mail->Password   = 'kski jcxi kofg yndl';
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('rehnuma.14.11@gmail.com', 'Grameen Setu');
    $mail->addAddress($user['email'], $user['full_name']);
    $mail->isHTML(true);
    $mail->Subject = 'New Verification Code - Grameen Setu';
    $mail->Body    = "<h2>Hello {$user['full_name']}</h2><p>Your new verification code is:</p><h1 style='font-size:32px;letter-spacing:5px;'>$new_code</h1>";
    $mail->send();
    $email_sent = true;
} catch (Exception $e) {
    error_log("Resend email failed: " . $e->getMessage());
}

$response = ['status' => 'success', 'message' => $email_sent ? 'New code sent to your email.' : 'Could not send email.'];
if (!$email_sent) {
    $response['verification_code'] = $new_code;
    $response['message'] .= " Code: $new_code";
}
echo json_encode($response);
$conn->close();
ob_end_flush();
?>
<?php
ob_start(); // ✅ START BUFFER

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0); // keep OFF for clean JSON

require_once 'config.php';

$user_id = $_POST['user_id'] ?? '';

if (empty($user_id)) {
    echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
    exit;
}

// Get user email and generate new code
$stmt = $conn->prepare("SELECT email, full_name FROM users WHERE id = ? AND is_verified = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User not found or already verified']);
    $stmt->close();
    $conn->close();
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();

// Generate new code
$new_code = sprintf("%06d", mt_rand(0, 999999));

// Update user
$stmt = $conn->prepare("UPDATE users SET verification_code = ? WHERE id = ?");
$stmt->bind_param("si", $new_code, $user_id);
$stmt->execute();
$stmt->close();

// Send email
require_once '../vendor/autoload.php';

$mail = new PHPMailer\PHPMailer\PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rehnuma.14.11@gmail.com';     // CHANGE THIS
    $mail->Password   = 'kski jcxi kofg yndl';        // CHANGE THIS
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    
    $mail->setFrom('rehnuma.14.11@gmail.com', 'Grameen Setu');
    $mail->addAddress($user['email'], $user['full_name']);
    
    $mail->isHTML(true);
    $mail->Subject = 'Your new verification code - Grameen Setu';
    $mail->Body    = "
        <h2>Hello {$user['full_name']}!</h2>
        <p>Your new verification code is:</p>
        <h1 style='font-size: 32px; letter-spacing: 5px; color: #166534;'>$new_code</h1>
        <p>Enter this code to verify your account.</p>
    ";
    
    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'New verification code sent to your email']);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Could not send email. Please try again.']);
}

$conn->close();
ob_end_flush();
?>
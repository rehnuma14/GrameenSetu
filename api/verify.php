<?php
ob_start(); // ✅ START BUFFER

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0); // keep OFF for clean JSON

require_once 'config.php';

$user_id = $_POST['user_id'] ?? '';
$code = trim($_POST['code'] ?? '');

if (empty($user_id) || empty($code)) {
    echo json_encode(['status' => 'error', 'message' => 'User ID and code are required']);
    exit;
}

// Verify code
$stmt = $conn->prepare("SELECT id FROM users WHERE id = ? AND verification_code = ? AND is_verified = 0");
$stmt->bind_param("is", $user_id, $code);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Update user as verified
    $stmt->close();
    $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    
    echo json_encode(['status' => 'success', 'message' => 'Email verified successfully! You can now log in.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired verification code']);
}

$stmt->close();
$conn->close();
ob_end_flush();
?>
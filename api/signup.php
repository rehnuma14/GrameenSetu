<?php
ob_start(); // ✅ START BUFFER

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0); // keep OFF for clean JSON

require_once 'config.php';

// Get POST data
$full_name = trim($_POST['full_name'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'farmer';

// Validate input
if (empty($full_name) || empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

if (strlen($password) < 4) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 4 characters']);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Generate 6-digit verification code
$verification_code = sprintf("%06d", mt_rand(0, 999999));

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, 0)");
$stmt->bind_param("sssss", $full_name, $email, $hashed_password, $role, $verification_code);

if ($stmt->execute()) {
    $user_id = $stmt->insert_id;
    $stmt->close();
    
    // Send verification email
    require_once '../vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rehnuma.14.11@gmail.com';     // CHANGE THIS
        $mail->Password   = 'kski jcxi kofg yndl';        // CHANGE THIS
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('rehnuma.14.11@gmail.com', 'Grameen Setu');
        $mail->addAddress($email, $full_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Verify your Grameen Setu account';
        $mail->Body    = "
            <h2>Welcome to Grameen Setu, $full_name!</h2>
            <p>Your verification code is:</p>
            <h1 style='font-size: 32px; letter-spacing: 5px; color: #166534;'>$verification_code</h1>
            <p>Enter this code in the verification popup to activate your account.</p>
            <p>This code will expire in 10 minutes.</p>
            <p>If you didn't create an account, please ignore this email.</p>
        ";
        $mail->AltBody = "Your verification code is: $verification_code";
        
        $mail->send();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful! Please check your email for the verification code.',
            'user_id' => $user_id,
            'email' => $email
        ]);
        
    } catch (Exception $e) {
        // Even if email fails, user is created (they can resend code)
        echo json_encode([
            'status' => 'success',
            'message' => 'Account created, but email could not be sent. Please contact support.',
            'user_id' => $user_id,
            'email' => $email,
            'email_error' => true
        ]);
    }
    
} else {
    echo json_encode(['status' => 'error', 'message' => 'Registration failed: ' . $conn->error]);
}

$conn->close();
ob_end_flush();
?>
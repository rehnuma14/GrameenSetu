<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'config.php';
session_start();

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
    exit;
}

try {
    // Get user from database
    $stmt = $conn->prepare("SELECT id, full_name, email, password, role, is_verified FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
        $conn->close();
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
        $conn->close();
        exit;
    }
    
    // Check if verified
    if (!$user['is_verified']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Please verify your email before logging in',
            'needs_verification' => true,
            'user_id' => (int)$user['id'],
            'email' => $user['email']
        ]);
        $conn->close();
        exit;
    }
    
    // Login successful
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Login failed: ' . $e->getMessage()]);
}

$conn->close();
?>

<?php
ob_start(); // ✅ START BUFFER

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0); // keep OFF for clean JSON

require_once 'config.php';
session_start();

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
    exit;
}

$stmt = $conn->prepare("SELECT id, full_name, email, password, role, is_verified FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
    $stmt->close();
    $conn->close();
    exit;
}

$user = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!password_verify($password, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password']);
    exit;
}

if ($user['is_verified'] == 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please verify your email before logging in',
        'needs_verification' => true,
        'user_id' => $user['id'],
        'email' => $user['email']
    ]);
    exit;
}

// Login successful - create session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['full_name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];

echo json_encode([
    'status' => 'success',
    'message' => 'Login successful!',
    'user' => [
        'id' => $user['id'],
        'name' => $user['full_name'],
        'email' => $user['email'],
        'role' => $user['role']
    ]
]);
$conn->close();
ob_end_flush();
?>
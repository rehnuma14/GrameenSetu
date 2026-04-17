<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    require_once 'config.php';

    $full_name = trim($_POST['full_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone_number'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'farmer';

    if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'All fields required']);
        exit;
    }

    // Check if phone column exists first - for MySQL < 8.0 compatibility
    $checkColumn = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'users' AND COLUMN_NAME = 'phone_number'");
    if ($checkColumn && $checkColumn->num_rows == 0) {
        if (!$conn->query("ALTER TABLE users ADD COLUMN phone_number VARCHAR(20) AFTER email")) {
            throw new Exception("ALTER TABLE failed: " . $conn->error);
        }
    }

    // Check duplicate email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception('Prepare SELECT failed: ' . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception('Execute SELECT failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already registered']);
        $stmt->close();
        $conn->close();
        exit;
    }
    $stmt->close();

    // Generate code and hash password
    $code = sprintf("%06d", mt_rand(0, 999999));
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, password, role, verification_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)");
    if (!$stmt) {
        throw new Exception('Prepare INSERT failed: ' . $conn->error);
    }

    $stmt->bind_param("ssssss", $full_name, $email, $phone, $hashed, $role, $code);
    if (!$stmt->execute()) {
        throw new Exception('Execute INSERT failed: ' . $stmt->error);
    }

    $user_id = $stmt->insert_id;
    $stmt->close();

    // Respond with success
    $response = [
        'status' => 'success',
        'message' => 'Account created! Check your email for the verification code.',
        'user_id' => $user_id,
        'email' => $email,
        'verification_code' => $code   // For development
    ];

    // Try to send email but don't fail if it doesn't work
    if (file_exists('../vendor/autoload.php')) {
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
            $mail->addAddress($email, $full_name);
            $mail->isHTML(true);
            $mail->Subject = 'Verify your Grameen Setu account';
            $mail->Body    = "<h2>Welcome to Grameen Setu!</h2><p>Code: $code</p>";
            $mail->send();
        } catch (Exception $e) {
            // Silently fail - response already prepared
        }
    }

    echo json_encode($response);
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}
?>

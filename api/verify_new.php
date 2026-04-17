<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once 'config.php';

$user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
$code = isset($_POST['code']) ? trim($_POST['code']) : '';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($user_id && $code !== '') {

    $stmt = $conn->prepare("SELECT id, full_name, email, role, verification_code, is_verified FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$user) {

        $response = ['status' => 'error', 'message' => 'User not found'];

    } elseif ($user['is_verified'] == 1) {

        $response = [
            'status' => 'success',
            'message' => 'Account already verified',
            'user' => [
                'id' => $user['id'],
                'name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ];

    } elseif ($code === trim($user['verification_code'])) {

        $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE id = ?");
        $update->bind_param("i", $user_id);

        if ($update->execute()) {

            // ✅ Safe mail (won’t break JSON)
            if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
                try {
                    require_once __DIR__ . '/../vendor/autoload.php';

                    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'rehnuma.14.11@gmail.com';
                    $mail->Password = 'kski jcxi kofg yndl';
                    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('rehnuma.14.11@gmail.com', 'Grameen Setu');
                    $mail->addAddress($user['email'], $user['full_name']);
                    $mail->isHTML(true);
                    $mail->Subject = 'Account Verified';
                    $mail->Body = "Your account is verified.";

                    $mail->send();
                } catch (Throwable $e) {
                    error_log("Mail error: " . $e->getMessage());
                }
            }

            $response = [
                'status' => 'success',
                'message' => '✅ Verification successful! Redirecting...',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ];

        } else {
            $response = ['status' => 'error', 'message' => 'Database update failed'];
        }

        $update->close();

    } else {

        $response = ['status' => 'error', 'message' => '❌ Invalid verification code'];

    }
}

// ✅ CLEAN OUTPUT (IMPORTANT)
echo json_encode($response);
$conn->close();
?>
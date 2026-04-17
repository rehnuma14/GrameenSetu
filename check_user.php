<?php
require_once 'api/config.php';

$result = $conn->query('SELECT id, email, is_verified, verification_code FROM users WHERE id = 21');
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo 'User ID: ' . $user['id'] . PHP_EOL;
    echo 'Email: ' . $user['email'] . PHP_EOL;
    echo 'Is verified: ' . $user['is_verified'] . PHP_EOL;
    echo 'Verification code: ' . $user['verification_code'] . PHP_EOL;
} else {
    echo 'User not found' . PHP_EOL;
}
ob_end_clean();
echo json_encode($response);
exit;
?>
<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

ob_start();

echo "Starting test...\n";

// Test 1: Check if config loads
if (!file_exists('config.php')) {
    echo "ERROR: config.php not found\n";
} else {
    echo "config.php found\n";
    try {
        require_once 'config.php';
        echo "config.php loaded\n";
        
        if ($conn->connect_error) {
            echo "DB Error: " . $conn->connect_error . "\n";
        } else {
            echo "DB connected\n";
            
            // Check if users table exists
            $result = $conn->query("DESCRIBE users");
            if (!$result) {
                echo "ERROR: users table issue - " . $conn->error . "\n";
            } else {
                echo "users table OK\n";
            }
        }
    } catch (Exception $e) {
        echo "Exception in config: " . $e->getMessage() . "\n";
    }
}

$output = ob_get_clean();
echo json_encode(['status' => 'test_complete', 'output' => $output]);
?>

<?php
// Connect to MySQL
$conn = new mysqli('localhost', 'root', '', 'grameen_setu');

if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

// Check if phone_number column exists
$result = $conn->query("SHOW COLUMNS FROM users LIKE 'phone_number'");

if ($result->num_rows == 0) {
    // Column doesn't exist, add it
    $sql = "ALTER TABLE users ADD COLUMN phone_number VARCHAR(20) AFTER email";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['status' => 'success', 'message' => 'Phone number column added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding column: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'success', 'message' => 'Phone number column already exists']);
}

$conn->close();
?>

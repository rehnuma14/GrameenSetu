<?php
// Connect to MySQL server (without database selected first)
$conn = new mysqli('localhost', 'root', '');

if ($conn->connect_error) {
    die(json_encode(['error' => $conn->connect_error]));
}

// Create database
if (!$conn->query("CREATE DATABASE IF NOT EXISTS grameen_setu")) {
    die("Database creation failed: " . $conn->error);
}

// Select database
if (!$conn->select_db("grameen_setu")) {
    die("Database selection failed: " . $conn->error);
}

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,  phone_number VARCHAR(20),  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'farmer',
  verification_code VARCHAR(10),
  is_verified TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if (!$conn->query($sql)) {
    die("Table creation failed: " . $conn->error);
}

// Insert test users
$hashed = password_hash('1234', PASSWORD_DEFAULT);

$conn->query("INSERT IGNORE INTO users (full_name, email, password, role, is_verified) VALUES ('Test Farmer', 'farmer@test.com', '$hashed', 'farmer', 1)");
$conn->query("INSERT IGNORE INTO users (full_name, email, password, role, is_verified) VALUES ('Test Buyer', 'buyer@test.com', '$hashed', 'buyer', 1)");

echo json_encode(['status' => 'success', 'message' => 'Database initialized successfully']);
$conn->close();
?>

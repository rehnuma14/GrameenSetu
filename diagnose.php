<?php
echo "<h3>MySQL Connection Diagnostics</h3>";

// Try different connection methods
$attempts = [
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'socket' => 'C:\\xampp\\mysql\\mysql.sock', 'desc' => 'localhost with socket'],
    ['host' => 'localhost', 'user' => 'root', 'pass' => '', 'socket' => '/tmp/mysql.sock', 'desc' => 'localhost with /tmp socket'],
    ['host' => '127.0.0.1', 'user' => 'root', 'pass' => '', 'port' => 3307, 'desc' => '127.0.0.1:3307'],
    ['host' => 'localhost', 'user' => 'pma', 'pass' => 'pmapass', 'desc' => 'localhost pma user (phpmyadmin)'],
    ['host' => 'localhost', 'user' => '', 'pass' => '', 'desc' => 'localhost anonymous'],
];

foreach ($attempts as $attempt) {
    $host = $attempt['host'];
    $user = $attempt['user'];
    $pass = $attempt['pass'];
    $socket = $attempt['socket'] ?? null;
    $port = $attempt['port'] ?? null;
    $desc = $attempt['desc'];
    
    echo "<p><strong>Trying: $desc</strong></p>";
    
    try {
        if ($socket) {
            $conn = @new mysqli($host, $user, $pass, '', 0, $socket);
        } elseif ($port) {
            $conn = @new mysqli($host, $user, $pass, '', $port);
        } else {
            $conn = @new mysqli($host, $user, $pass);
        }
        
        if ($conn && !$conn->connect_error) {
            echo "<span style='color: green;'>✓ SUCCESS - Connected!</span><br>";
            echo "Server version: " . $conn->server_info . "<br>";
            echo "Protocol version: " . $conn->protocol_version . "<br>";
            
            // Try to create the database
            if ($conn->query("CREATE DATABASE IF NOT EXISTS grameen_setu")) {
                echo "✓ Database 'grameen_setu' created/exists<br>";
            }
            
            // Try to select it
            if ($conn->select_db("grameen_setu")) {
                echo "✓ Database selected<br>";
                
                // Create table
                $sql = "CREATE TABLE IF NOT EXISTS users (
                  id INT PRIMARY KEY AUTO_INCREMENT,
                  full_name VARCHAR(255) NOT NULL,
                  email VARCHAR(255) UNIQUE NOT NULL,
                  password VARCHAR(255) NOT NULL,
                  role VARCHAR(50) NOT NULL DEFAULT 'farmer',
                  verification_code VARCHAR(10),
                  is_verified TINYINT(1) DEFAULT 0,
                  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";
                
                if ($conn->query($sql)) {
                    echo "✓ Users table created/exists<br>";
                }
                
                echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px; margin-top: 20px;'>";
                echo "<strong style='font-size: 18px; color: green;'>✓✓✓ MySQL IS FULLY CONFIGURED! ✓✓✓</strong><br>";
                echo "Successful connection:<br>";
                echo "Host: $host<br>";
                echo "User: $user<br>";
                echo "Pass: " . ($pass ? '(set)' : '(empty)') . "<br>";
                if ($socket) echo "Socket: $socket<br>";
                if ($port) echo "Port: $port<br>";
                echo "</p>";
            }
            
            $conn->close();
            break; // Stop after first success
        } else {
            echo "<span style='color: red;'>✗ Failed - " . ($conn ? $conn->connect_error : 'Connection null') . "</span><br>";
        }
    } catch (Exception $e) {
        echo "<span style='color: red;'>✗ Exception - " . $e->getMessage() . "</span><br>";
    }
    echo "<br>";
}
?>

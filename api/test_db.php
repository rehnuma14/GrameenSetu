<?php
ob_start(); // ✅ START BUFFER

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0); // keep OFF for clean JSON

require_once 'config.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/config.php';

if (!isset($conn) || !is_object($conn)) {
    die("❌ Database connection unavailable. Check config.php and connection setup.");
}

if ($conn instanceof mysqli) {
    if ($conn->connect_error) {
        die("❌ Connection failed: " . $conn->connect_error);
    }
    $databaseName = defined('DB_NAME') ? DB_NAME : 'unknown';
    echo "✅ Connected successfully to database: " . $databaseName;
    $conn->close();
    exit;
}

if (isset($conn->pdo) && $conn->pdo instanceof PDO) {
    try {
        $conn->pdo->query('SELECT 1');
    } catch (Exception $e) {
        die("❌ SQLite connection failed: " . $e->getMessage());
    }
    $databaseName = defined('DB_NAME') ? DB_NAME : (defined('DB_FILE') ? DB_FILE : 'unknown');
    echo "✅ Connected successfully to database: " . $databaseName;
    exit;
}

if (isset($conn->error) && $conn->error) {
    die("❌ Database connection error: " . $conn->error);
}

die("❌ Database connection unavailable. Unsupported connection object type.");
ob_end_clean();
echo json_encode($response);
exit;
?>
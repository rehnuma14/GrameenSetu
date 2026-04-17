<?php
ob_start(); // ✅ START BUFFER

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

error_reporting(E_ALL);
ini_set('display_errors', 0); // keep OFF for clean JSON

require_once 'config.php';
session_start();
session_destroy();
echo json_encode(['status' => 'success', 'message' => 'Logged out successfully']);
ob_end_flush();
?>
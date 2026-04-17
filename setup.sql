-- Create database
CREATE DATABASE IF NOT EXISTS grameen_setu;
USE grameen_setu;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  full_name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) NOT NULL DEFAULT 'farmer',
  verification_code VARCHAR(10),
  is_verified TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Optional: Create test users (passwords: 1234)
INSERT INTO users (full_name, email, password, role, is_verified) VALUES
('Test Farmer', 'farmer@test.com', '$2y$10$YmFjazJiYWNrMmJhY2syYm.1Q9P5p5FhFnpSqW4J9Z5nYpZ5nYpZ5', 'farmer', 1),
('Test Buyer', 'buyer@test.com', '$2y$10$YmFjazJiYWNrMmJhY2syYm.1Q9P5p5FhFnpSqW4J9Z5nYpZ5nYpZ5', 'buyer', 1);

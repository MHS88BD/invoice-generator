<?php
// config.php

// Database Configuration (MySQL)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'invoice-app');
define('DB_USER', 'invoice-user');
define('DB_PASS', '0k9eqHAuilT389QQyOj1');

try {
    // Connect to MySQL
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    
    // Set errormode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Create tables if they do not exist
    
    // Users table for OTP login
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        mobile_number VARCHAR(20) UNIQUE NOT NULL,
        otp_code VARCHAR(10),
        otp_expiry DATETIME,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB");

    // Invoices table
    $pdo->exec("CREATE TABLE IF NOT EXISTS invoices (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        invoice_number VARCHAR(50),
        invoice_date DATE,
        sender_mobile VARCHAR(20),
        sender_email VARCHAR(255),
        sender_address TEXT,
        recipient_mobile VARCHAR(20),
        recipient_email VARCHAR(255),
        recipient_name VARCHAR(100),
        recipient_address TEXT,
        items JSON, 
        subtotal DECIMAL(10,2),
        tax_rate DECIMAL(5,2), 
        tax_amount DECIMAL(10,2),
        total DECIMAL(10,2),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB");

} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage() . "<br>Please check `config.php` details.");
}

// Start Session
session_start();

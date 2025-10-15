<?php
// Database configuration with Railway support
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'dn_tours';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$port = getenv('DB_PORT') ?: '3306';

try {
    // Check if PDO MySQL driver is available
    if (!extension_loaded('pdo_mysql')) {
        die("Connection failed: PDO MySQL driver is not installed. Please install php-pdo-mysql extension.");
    }
    
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch(PDOException $e) {
    // More informative error message
    error_log("Database connection failed: " . $e->getMessage());
    die("Connection failed: " . $e->getMessage() . "<br><br>Please check your database configuration.");
}
?>
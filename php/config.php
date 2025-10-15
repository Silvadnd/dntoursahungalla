<?php
// Suppress any output if this is an AJAX/JSON request
$isAjaxRequest = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                 strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
$isJsonRequest = isset($_SERVER['CONTENT_TYPE']) && 
                 strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;

// Database configuration with Railway support
$host = getenv('DB_HOST') ?: getenv('MYSQLHOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: getenv('MYSQLDATABASE') ?: 'dn_tours';
$username = getenv('DB_USER') ?: getenv('MYSQLUSER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: getenv('MYSQLPASSWORD') ?: '';
$port = getenv('DB_PORT') ?: getenv('MYSQLPORT') ?: '3306';

// Check if we're on Railway (they provide RAILWAY_ENVIRONMENT)
$isRailway = getenv('RAILWAY_ENVIRONMENT') !== false;

// For Railway, use the direct MYSQL_URL if available
$mysqlUrl = getenv('MYSQL_URL');

try {
    // Check if PDO MySQL driver is available
    if (!extension_loaded('pdo_mysql')) {
        $errorMsg = "Connection failed: PDO MySQL driver is not installed. Please install php-pdo-mysql extension.";
        error_log($errorMsg);
        throw new Exception($errorMsg);
    }
    
    // Parse MYSQL_URL if available (Railway format)
    if ($mysqlUrl && $isRailway) {
        // Format: mysql://user:password@host:port/database
        $urlParts = parse_url($mysqlUrl);
        $host = $urlParts['host'] ?? $host;
        $username = $urlParts['user'] ?? $username;
        $password = $urlParts['pass'] ?? $password;
        $port = $urlParts['port'] ?? $port;
        $dbname = ltrim($urlParts['path'] ?? '', '/') ?: $dbname;
    }
    
    // Force TCP/IP connection by using 127.0.0.1 instead of localhost when local
    if ($host === 'localhost' && !$isRailway) {
        $host = '127.0.0.1';
    }
    
    // Build DSN with proper formatting for cloud hosting
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    
    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_TIMEOUT => 10, // 10 second timeout
            PDO::ATTR_PERSISTENT => false // Don't use persistent connections on cloud
        ]
    );
} catch(PDOException $e) {
    // Log the error
    error_log("Database connection failed: " . $e->getMessage());
    
    // Store error info for scripts that need it
    $db_error = $e->getMessage();
    $db_connection_failed = true;
    
    // For JSON/API requests, let the calling script handle the error
    // For regular page loads, show user-friendly error
    if (!$isAjaxRequest && !$isJsonRequest && 
        !in_array(basename($_SERVER['PHP_SELF']), ['get_reviews.php', 'submit_review.php'])) {
        
        $errorMsg = "Connection failed: " . $e->getMessage();
        
        if (getenv('RAILWAY_ENVIRONMENT') === false) {
            // Local development - show more details
            $errorMsg .= "<br><br><strong>Debug Info:</strong><br>";
            $errorMsg .= "Host: " . htmlspecialchars($host) . "<br>";
            $errorMsg .= "Port: " . htmlspecialchars($port) . "<br>";
            $errorMsg .= "Database: " . htmlspecialchars($dbname) . "<br>";
            $errorMsg .= "User: " . htmlspecialchars($username) . "<br>";
            $errorMsg .= "<br>Please check your database configuration.";
        } else {
            // Production - generic message
            $errorMsg .= "<br><br>Please check your database configuration and ensure the MySQL service is running.";
        }
        
        die($errorMsg);
    }
    
    // For API requests, throw exception to be caught by calling script
    throw $e;
}
?>
<?php
/**
 * Railway-specific Database Configuration
 * Use this if you're having connection issues on Railway
 * 
 * Rename this file to config.php on Railway deployment
 */

// Railway automatically provides these environment variables
$mysqlHost = getenv('MYSQLHOST');
$mysqlUser = getenv('MYSQLUSER');
$mysqlPassword = getenv('MYSQLPASSWORD');
$mysqlDatabase = getenv('MYSQLDATABASE');
$mysqlPort = getenv('MYSQLPORT') ?: '3306';

// Alternative: Use MYSQL_URL (Railway's connection string format)
$mysqlUrl = getenv('MYSQL_URL');

try {
    // Check if PDO MySQL driver is available
    if (!extension_loaded('pdo_mysql')) {
        throw new Exception("PDO MySQL driver is not installed. Check nixpacks.toml configuration.");
    }
    
    // If MYSQL_URL is available, parse it
    if ($mysqlUrl) {
        $urlParts = parse_url($mysqlUrl);
        $mysqlHost = $urlParts['host'];
        $mysqlUser = $urlParts['user'];
        $mysqlPassword = $urlParts['pass'];
        $mysqlPort = $urlParts['port'] ?? '3306';
        $mysqlDatabase = ltrim($urlParts['path'], '/');
    }
    
    // Validate required variables
    if (empty($mysqlHost) || empty($mysqlUser) || empty($mysqlDatabase)) {
        throw new Exception("Missing required database environment variables. Please check Railway configuration.");
    }
    
    // Build connection string - use TCP/IP explicitly
    $dsn = sprintf(
        "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
        $mysqlHost,
        $mysqlPort,
        $mysqlDatabase
    );
    
    // Create PDO connection
    $pdo = new PDO(
        $dsn,
        $mysqlUser,
        $mysqlPassword,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_TIMEOUT => 15,
            PDO::ATTR_PERSISTENT => false,
            // Force TCP/IP connection (not Unix socket)
            PDO::MYSQL_ATTR_LOCAL_INFILE => false
        ]
    );
    
    // Test the connection
    $pdo->query('SELECT 1');
    
} catch(PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    
    http_response_code(500);
    die(json_encode([
        'error' => 'Database connection failed',
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'hint' => 'Check Railway MySQL service and environment variables'
    ]));
    
} catch(Exception $e) {
    error_log("Configuration error: " . $e->getMessage());
    
    http_response_code(500);
    die(json_encode([
        'error' => 'Configuration error',
        'message' => $e->getMessage()
    ]));
}
?>

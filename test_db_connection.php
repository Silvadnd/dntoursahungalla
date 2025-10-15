<?php
/**
 * Database Connection Test Script
 * Access this file at: https://your-domain.railway.app/test_db_connection.php
 * 
 * This will help diagnose Railway database connection issues
 * IMPORTANT: Delete this file after debugging!
 */

header('Content-Type: application/json');

$results = [
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => [],
    'php_info' => [],
    'connection_attempts' => []
];

// Check environment variables
$envVars = [
    'MYSQL_URL',
    'MYSQLHOST',
    'MYSQLUSER',
    'MYSQLDATABASE',
    'MYSQLPORT',
    'MYSQLPASSWORD',
    'DB_HOST',
    'DB_NAME',
    'DB_USER',
    'DB_PORT',
    'RAILWAY_ENVIRONMENT'
];

foreach ($envVars as $var) {
    $value = getenv($var);
    if ($var === 'MYSQLPASSWORD' || $var === 'DB_PASSWORD') {
        $results['environment'][$var] = $value ? '***SET***' : 'NOT SET';
    } else {
        $results['environment'][$var] = $value ?: 'NOT SET';
    }
}

// Check PHP extensions
$results['php_info']['php_version'] = PHP_VERSION;
$results['php_info']['pdo_available'] = extension_loaded('pdo');
$results['php_info']['pdo_mysql_available'] = extension_loaded('pdo_mysql');
$results['php_info']['mysqli_available'] = extension_loaded('mysqli');

// Attempt 1: Using MYSQL_URL
$mysqlUrl = getenv('MYSQL_URL');
if ($mysqlUrl) {
    try {
        $urlParts = parse_url($mysqlUrl);
        $host = $urlParts['host'];
        $user = $urlParts['user'];
        $pass = $urlParts['pass'];
        $port = $urlParts['port'] ?? '3306';
        $dbname = ltrim($urlParts['path'], '/');
        
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 10
        ]);
        
        $results['connection_attempts'][] = [
            'method' => 'MYSQL_URL',
            'status' => 'SUCCESS',
            'host' => $host,
            'port' => $port,
            'database' => $dbname
        ];
    } catch (PDOException $e) {
        $results['connection_attempts'][] = [
            'method' => 'MYSQL_URL',
            'status' => 'FAILED',
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ];
    }
}

// Attempt 2: Using individual env variables (Railway format)
$mysqlHost = getenv('MYSQLHOST');
$mysqlUser = getenv('MYSQLUSER');
$mysqlPassword = getenv('MYSQLPASSWORD');
$mysqlDatabase = getenv('MYSQLDATABASE');
$mysqlPort = getenv('MYSQLPORT') ?: '3306';

if ($mysqlHost && $mysqlUser && $mysqlDatabase) {
    try {
        $dsn = "mysql:host=$mysqlHost;port=$mysqlPort;dbname=$mysqlDatabase;charset=utf8mb4";
        $pdo = new PDO($dsn, $mysqlUser, $mysqlPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 10
        ]);
        
        $results['connection_attempts'][] = [
            'method' => 'MYSQL* variables',
            'status' => 'SUCCESS',
            'host' => $mysqlHost,
            'port' => $mysqlPort,
            'database' => $mysqlDatabase
        ];
    } catch (PDOException $e) {
        $results['connection_attempts'][] = [
            'method' => 'MYSQL* variables',
            'status' => 'FAILED',
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ];
    }
}

// Attempt 3: Using DB_* variables
$dbHost = getenv('DB_HOST');
$dbUser = getenv('DB_USER');
$dbPassword = getenv('DB_PASSWORD');
$dbName = getenv('DB_NAME');
$dbPort = getenv('DB_PORT') ?: '3306';

if ($dbHost && $dbUser && $dbName) {
    try {
        $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 10
        ]);
        
        $results['connection_attempts'][] = [
            'method' => 'DB_* variables',
            'status' => 'SUCCESS',
            'host' => $dbHost,
            'port' => $dbPort,
            'database' => $dbName
        ];
    } catch (PDOException $e) {
        $results['connection_attempts'][] = [
            'method' => 'DB_* variables',
            'status' => 'FAILED',
            'error' => $e->getMessage(),
            'code' => $e->getCode()
        ];
    }
}

// Overall status
$hasSuccess = false;
foreach ($results['connection_attempts'] as $attempt) {
    if ($attempt['status'] === 'SUCCESS') {
        $hasSuccess = true;
        break;
    }
}

$results['overall_status'] = $hasSuccess ? 'DATABASE CONNECTION OK' : 'DATABASE CONNECTION FAILED';

// Add recommendations
if (!$hasSuccess) {
    $results['recommendations'] = [
        'Check that MySQL service is added to Railway project',
        'Verify environment variables are set correctly',
        'Ensure nixpacks.toml includes pdo_mysql extension',
        'Check Railway deployment logs for errors',
        'Verify database schema has been imported'
    ];
}

echo json_encode($results, JSON_PRETTY_PRINT);
?>

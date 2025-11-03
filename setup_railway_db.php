<?php
/**
 * Railway MySQL Database Setup Script
 * This script connects to Railway MySQL and creates all required tables
 */

// Railway connection details
$host = 'shuttle.proxy.rlwy.net';
$port = 16503;
$username = 'root';
$password = 'eTaArsItbOApYBCxEVGaupBEiqLkOFLN';
$dbname = 'railway';

echo "=== DN Tours Ahungalla - Railway Database Setup ===\n\n";
echo "Connecting to Railway MySQL...\n";
echo "Host: {$host}:{$port}\n";
echo "Database: {$dbname}\n\n";

try {
    // Create DSN with TCP protocol
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4;protocol=TCP";
    
    // Connect to MySQL
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    echo "✓ Successfully connected to Railway MySQL!\n\n";
    
    // Select the database
    echo "Using database: {$dbname}\n";
    $pdo->exec("USE {$dbname}");
    
    // SQL statements to create tables
    $sql_statements = [
        // Create reviews table
        "CREATE TABLE IF NOT EXISTS reviews (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255),
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            review_text TEXT NOT NULL,
            image_url VARCHAR(500),
            tour_photos TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            approved TINYINT(1) DEFAULT 1
        )" => "reviews table",
        
        // Create gallery table
        "CREATE TABLE IF NOT EXISTS gallery (
            id INT AUTO_INCREMENT PRIMARY KEY,
            image_url VARCHAR(500) NOT NULL,
            image_name VARCHAR(255) NOT NULL,
            category VARCHAR(100) DEFAULT 'general',
            uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )" => "gallery table",
        
        // Create admin users table
        "CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )" => "admin_users table",
    ];
    
    // Execute CREATE TABLE statements
    echo "\nCreating tables...\n";
    foreach ($sql_statements as $sql => $name) {
        try {
            $pdo->exec($sql);
            echo "✓ Created {$name}\n";
        } catch (PDOException $e) {
            echo "! Error creating {$name}: " . $e->getMessage() . "\n";
        }
    }
    
    // Insert default admin user
    echo "\nInserting default admin user...\n";
    $check_admin = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $check_admin->execute(['admin']);
    
    if ($check_admin->rowCount() == 0) {
        $insert_admin = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        // Password: admin123 (hashed with bcrypt)
        $admin_password = '$2y$10$tt6UNic8UUDyAlmzRkwFquP58dyPxcXdmX1..TCfkfwwSSOkvD5/K';
        $insert_admin->execute(['admin', $admin_password]);
        echo "✓ Default admin user created (username: admin, password: admin123)\n";
    } else {
        echo "! Admin user already exists\n";
    }
    
    // Verify tables exist
    echo "\nVerifying database structure...\n";
    $tables = ['reviews', 'gallery', 'admin_users'];
    $tables_check = $pdo->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?");
    $tables_check->execute([$dbname]);
    $existing_tables = $tables_check->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        if (in_array($table, $existing_tables)) {
            // Count rows
            $count = $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
            echo "✓ {$table} ({$count} rows)\n";
        } else {
            echo "✗ {$table} NOT FOUND\n";
        }
    }
    
    echo "\n=== Database Setup Complete! ===\n";
    echo "Your website is now ready to use with Railway MySQL!\n";
    echo "\nYou can now:\n";
    echo "1. Access your website on Railway\n";
    echo "2. Submit reviews (they will be saved to Railway database)\n";
    echo "3. Login to admin panel with username: admin, password: admin123\n";
    
} catch (PDOException $e) {
    echo "✗ Connection Error: " . $e->getMessage() . "\n\n";
    echo "Troubleshooting:\n";
    echo "1. Verify your Railway MySQL credentials are correct\n";
    echo "2. Ensure Railway MySQL service is running\n";
    echo "3. Check if the firewall allows connection to shuttle.proxy.rlwy.net:16503\n";
    echo "4. Try connecting via Railway CLI: railway connect MySQL\n";
    exit(1);
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

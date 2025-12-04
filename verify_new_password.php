<?php
require_once 'php/config.php';

echo "=== ADMIN LOGIN TEST WITH NEW PASSWORD ===\n\n";

$test_username = 'admin';
$test_password = 'AdMin@2026';

try {
    // Simulate login
    $stmt = $pdo->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
    $stmt->execute([$test_username]);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✓ Admin user found\n";
        echo "Username: " . $admin['username'] . "\n\n";
        
        if (password_verify($test_password, $admin['password'])) {
            echo "✓ Password 'AdMin@2026' is CORRECT\n";
            echo "✓ Login would SUCCEED\n";
            echo "✓ Admin ID: " . $admin['id'] . "\n\n";
            echo "=== STATUS ===\n";
            echo "✅ NEW CREDENTIALS WORKING\n\n";
            echo "Username: admin\n";
            echo "Password: AdMin@2026\n";
        } else {
            echo "✗ Password verification FAILED\n";
            echo "✗ Login would FAIL\n";
        }
    } else {
        echo "✗ Admin user not found!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

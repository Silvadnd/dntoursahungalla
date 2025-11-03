<?php
require_once 'php/config.php';

echo "=== ADMIN PASSWORD VERIFICATION ===\n\n";

try {
    // Get admin user
    $check = $pdo->prepare('SELECT username, password FROM admin_users WHERE username = ?');
    $check->execute(['admin']);
    $admin = $check->fetch();
    
    if ($admin) {
        echo "✓ Admin user found\n";
        echo "Username: " . $admin['username'] . "\n";
        echo "Password Hash: " . substr($admin['password'], 0, 30) . "...\n\n";
        
        echo "=== PASSWORD VERIFICATION ===\n";
        
        // Test with 'admin'
        $pass1 = password_verify('admin', $admin['password']);
        echo ($pass1 ? "✓ " : "✗ ") . "Password 'admin': ";
        echo ($pass1 ? "CORRECT" : "INCORRECT") . "\n";
        
        // Test with 'admin123'
        $pass2 = password_verify('admin123', $admin['password']);
        echo ($pass2 ? "✓ " : "✗ ") . "Password 'admin123': ";
        echo ($pass2 ? "CORRECT" : "INCORRECT") . "\n";
        
        echo "\n=== VERDICT ===\n";
        if ($pass1) {
            echo "✓ Use login: admin / admin\n";
        } elseif ($pass2) {
            echo "✓ Use login: admin / admin123\n";
        } else {
            echo "✗ Neither password matches!\n";
            echo "Need to reset password.\n";
        }
        
    } else {
        echo "✗ Admin user NOT found in database!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

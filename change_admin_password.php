<?php
require_once 'php/config.php';

echo "=== ADMIN PASSWORD CHANGE ===\n\n";

$new_password = 'AdMin@2026';
$username = 'admin';

try {
    // Hash the new password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    
    echo "Updating password for user: $username\n";
    echo "New password: " . str_repeat('*', strlen($new_password)) . "\n\n";
    
    // Update the password
    $update = $pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
    $update->execute([$hashed_password, $username]);
    
    if ($update->rowCount() > 0) {
        echo "✓ Password updated successfully!\n\n";
        
        // Verify the change
        $verify = $pdo->prepare("SELECT username, password FROM admin_users WHERE username = ?");
        $verify->execute([$username]);
        $admin = $verify->fetch();
        
        if ($admin) {
            echo "=== VERIFICATION ===\n";
            echo "Username: " . $admin['username'] . "\n";
            echo "Password Hash: " . substr($admin['password'], 0, 30) . "...\n\n";
            
            // Test the new password
            echo "=== PASSWORD TEST ===\n";
            if (password_verify($new_password, $admin['password'])) {
                echo "✓ New password 'AdMin@2026' works correctly!\n\n";
                echo "=== LOGIN CREDENTIALS ===\n";
                echo "Username: admin\n";
                echo "Password: AdMin@2026\n\n";
                echo "You can now login with these credentials.\n";
            } else {
                echo "✗ Password verification failed!\n";
            }
        }
    } else {
        echo "✗ No rows updated. User 'admin' not found!\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
?>

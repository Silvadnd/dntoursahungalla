<?php
/**
 * Admin Login Test
 * Simulates the login process to verify credentials work
 */

require_once 'php/config.php';

echo "=== ADMIN LOGIN VERIFICATION TEST ===\n\n";

function test_login($username, $password) {
    global $pdo;
    
    echo "Testing login: $username / " . str_repeat('*', strlen($password)) . "\n";
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();
        
        if ($admin) {
            echo "  ✓ User found in database\n";
            
            if (password_verify($password, $admin['password'])) {
                echo "  ✓ Password matches!\n";
                echo "  ✓ Login would succeed\n";
                echo "  ✓ Admin ID: " . $admin['id'] . "\n";
                return true;
            } else {
                echo "  ✗ Password does NOT match\n";
                echo "  ✗ Login would fail\n";
                return false;
            }
        } else {
            echo "  ✗ User NOT found in database\n";
            return false;
        }
    } catch (Exception $e) {
        echo "  ✗ Error: " . $e->getMessage() . "\n";
        return false;
    }
}

echo "Test 1: admin / admin\n";
$test1 = test_login('admin', 'admin');
echo "\n";

echo "Test 2: admin / admin123\n";
$test2 = test_login('admin', 'admin123');
echo "\n";

echo "=== SUMMARY ===\n";
if ($test1) {
    echo "✓ CORRECT CREDENTIALS: admin / admin\n";
} elseif ($test2) {
    echo "✓ CORRECT CREDENTIALS: admin / admin123\n";
} else {
    echo "✗ NO WORKING CREDENTIALS FOUND\n";
    echo "✗ Need to reset admin password\n";
}

echo "\n=== HOW TO ACCESS ADMIN PANEL ===\n";
echo "1. Open: http://localhost/dntoursahungalla/php/admin_login.php\n";
if ($test1) {
    echo "2. Enter: admin / admin\n";
} elseif ($test2) {
    echo "2. Enter: admin / admin123\n";
}
echo "3. Click: Login\n";
echo "4. You should see: Admin Dashboard\n";

?>

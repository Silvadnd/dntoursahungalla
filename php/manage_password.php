<?php
/**
 * SECURE Admin Password Management
 * 
 * SECURITY NOTICE:
 * - Passwords are never stored in plain text
 * - Passwords are never logged or displayed
 * - Passwords are always hashed with bcrypt
 * - Only database has access to password hashes
 * - This file should be deleted after use or protected
 */

require_once 'php/config.php';
require_once 'php/auth.php';

// IMPORTANT: This should only be accessible locally or with authentication
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "This script accepts POST requests only.\n";
    exit;
}

$action = $_POST['action'] ?? '';
$username = $_POST['username'] ?? 'admin';
$new_password = $_POST['password'] ?? '';

// Verify password is strong enough
if (strlen($new_password) < 8) {
    echo json_encode([
        'success' => false,
        'message' => 'Password must be at least 8 characters long'
    ]);
    exit;
}

try {
    switch ($action) {
        case 'change_password':
            if ($auth->changePassword($username, $new_password)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Password updated successfully',
                    'username' => $username
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to update password'
                ]);
            }
            break;
            
        case 'verify_password':
            // Verify a password works without logging the plain password
            $admin = $auth->verifyLogin($username, $new_password);
            if ($admin) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Password is correct',
                    'username' => $admin['username']
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Password is incorrect'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Unknown action'
            ]);
    }
} catch (Exception $e) {
    error_log("Password management error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred'
    ]);
}
?>

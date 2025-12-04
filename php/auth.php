<?php
/**
 * Secure Authentication Helper
 * Handles password verification without exposing plain text passwords
 * Use this instead of hardcoding passwords in scripts
 */

require_once 'config.php';

class AdminAuth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Verify admin login credentials
     * @param string $username
     * @param string $password
     * @return array|false Admin user data if successful, false otherwise
     */
    public function verifyLogin($username, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, username, password FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Don't return the password hash
                return [
                    'id' => $admin['id'],
                    'username' => $admin['username']
                ];
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Login verification error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update admin password securely
     * @param string $username
     * @param string $new_password
     * @return bool True if successful, false otherwise
     */
    public function changePassword($username, $new_password) {
        try {
            // Validate password strength
            if (strlen($new_password) < 8) {
                error_log("Password too short for user: $username");
                return false;
            }
            
            // Hash the password
            $hashed = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            // Update in database
            $stmt = $this->pdo->prepare("UPDATE admin_users SET password = ? WHERE username = ?");
            $result = $stmt->execute([$hashed, $username]);
            
            if ($result) {
                error_log("Password changed for user: $username");
                return true;
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Password change error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if admin exists
     * @param string $username
     * @return bool
     */
    public function adminExists($username) {
        try {
            $stmt = $this->pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Admin check error: " . $e->getMessage());
            return false;
        }
    }
}

// Create global instance
$auth = new AdminAuth($pdo);
?>

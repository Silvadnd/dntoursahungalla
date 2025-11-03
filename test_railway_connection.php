<?php
/**
 * Railway Connection Test
 * Visit this page to verify your Railway MySQL connection is working
 * DELETE THIS FILE AFTER TESTING (security risk)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Railway MySQL Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #ff7e29;
            padding-bottom: 10px;
        }
        .test {
            margin: 20px 0;
            padding: 15px;
            border-left: 4px solid #ddd;
            border-radius: 4px;
        }
        .test.success {
            background: #e8f5e9;
            border-left-color: #4caf50;
        }
        .test.error {
            background: #ffebee;
            border-left-color: #f44336;
        }
        .test.info {
            background: #e3f2fd;
            border-left-color: #2196f3;
        }
        .test-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .test-detail {
            font-size: 14px;
            color: #666;
            margin-top: 8px;
        }
        code {
            background: #f4f4f4;
            padding: 4px 8px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f9f9f9;
            font-weight: bold;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Railway MySQL Connection Test</h1>
        
        <?php
        // Test configuration
        $host = 'shuttle.proxy.rlwy.net';
        $port = 16503;
        $username = 'root';
        $password = 'eTaArsItbOApYBCxEVGaupBEiqLkOFLN';
        $dbname = 'railway';
        
        echo '<div class="test info">';
        echo '<div class="test-title">📋 Connection Details</div>';
        echo '<table>';
        echo '<tr><th>Setting</th><th>Value</th></tr>';
        echo '<tr><td>Host</td><td><code>' . htmlspecialchars($host) . '</code></td></tr>';
        echo '<tr><td>Port</td><td><code>' . htmlspecialchars($port) . '</code></td></tr>';
        echo '<tr><td>Username</td><td><code>' . htmlspecialchars($username) . '</code></td></tr>';
        echo '<tr><td>Database</td><td><code>' . htmlspecialchars($dbname) . '</code></td></tr>';
        echo '<tr><td>Protocol</td><td><code>TCP</code></td></tr>';
        echo '</table>';
        echo '</div>';
        
        // Test 1: Connection
        echo '<div class="test';
        try {
            $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            echo ' success">';
            echo '<div class="test-title">✅ Connection: SUCCESS</div>';
            echo '<div class="test-detail">Successfully connected to Railway MySQL!</div>';
            
            // Test 2: Select Database
            echo '</div><div class="test';
            try {
                $pdo->exec("USE {$dbname}");
                echo ' success">';
                echo '<div class="test-title">✅ Database Selection: SUCCESS</div>';
                echo '<div class="test-detail">Using database: <code>' . htmlspecialchars($dbname) . '</code></div>';
                
                // Test 3: Check Tables
                echo '</div><div class="test';
                $tables_check = $pdo->prepare("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = ?");
                $tables_check->execute([$dbname]);
                $existing_tables = $tables_check->fetchAll(PDO::FETCH_COLUMN);
                
                $required_tables = ['reviews', 'gallery', 'admin_users'];
                $all_exist = true;
                
                foreach ($required_tables as $table) {
                    if (!in_array($table, $existing_tables)) {
                        $all_exist = false;
                        break;
                    }
                }
                
                if ($all_exist) {
                    echo ' success">';
                    echo '<div class="test-title">✅ Database Tables: SUCCESS</div>';
                    echo '<div class="test-detail">All required tables exist:</div>';
                    echo '<table>';
                    foreach ($required_tables as $table) {
                        $count = $pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
                        echo '<tr><td>' . htmlspecialchars($table) . '</td><td>' . $count . ' rows</td></tr>';
                    }
                    echo '</table>';
                } else {
                    echo ' error">';
                    echo '<div class="test-title">❌ Database Tables: INCOMPLETE</div>';
                    echo '<div class="test-detail">Missing tables:</div>';
                    foreach ($required_tables as $table) {
                        $status = in_array($table, $existing_tables) ? '✓' : '✗';
                        echo '<div>' . $status . ' ' . htmlspecialchars($table) . '</div>';
                    }
                }
                
            } catch (Exception $e) {
                echo ' error">';
                echo '<div class="test-title">❌ Database Selection: FAILED</div>';
                echo '<div class="test-detail">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            
        } catch (PDOException $e) {
            echo ' error">';
            echo '<div class="test-title">❌ Connection: FAILED</div>';
            echo '<div class="test-detail">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            echo '<div class="test-detail"><strong>Troubleshooting:</strong></div>';
            echo '<ul>';
            echo '<li>Check Railway MySQL service is running</li>';
            echo '<li>Verify firewall allows connection to shuttle.proxy.rlwy.net:16503</li>';
            echo '<li>Ensure credentials are correct</li>';
            echo '<li>Try: <code>railway connect MySQL</code></li>';
            echo '</ul>';
        }
        echo '</div>';
        ?>
        
        <div class="warning">
            <strong>⚠️ Security Warning:</strong><br>
            This test file contains database credentials and should be <strong>DELETED</strong> after testing:
            <ol>
                <li>After verifying connection works, delete this file from your server</li>
                <li>On Railway: Delete via Railway dashboard or SSH</li>
                <li>Or keep it but password-protect it (not recommended)</li>
            </ol>
            <strong>Command to delete:</strong><br>
            <code>rm test_railway_connection.php</code>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p><strong>Next Steps:</strong></p>
            <ol>
                <li>Verify all tests passed ✓</li>
                <li>Visit <a href="../">home page</a> and test features</li>
                <li>Submit a review to test database functionality</li>
                <li>Check About section to see if review appears</li>
                <li>Delete this test file when done</li>
            </ol>
        </div>
    </div>
</body>
</html>

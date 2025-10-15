<?php
// Prevent any output before JSON
ob_start();

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Suppress error display (log only)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Clean output buffer
ob_clean();

try {
    require_once 'config.php';
    
    // Check if database connection exists
    if (!isset($pdo)) {
        throw new Exception('Database connection not established');
    }
    
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE approved = 1 ORDER BY created_at DESC");
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'reviews' => $reviews,
        'count' => count($reviews)
    ]);
    
} catch(PDOException $e) {
    // Database error
    echo json_encode([
        'success' => false,
        'message' => 'Unable to retrieve reviews. Please try again later.',
        'error' => $e->getMessage()
    ]);
} catch(Exception $e) {
    // General error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// End output buffering
ob_end_flush();
?>
<?php
/**
 * Image Optimization Script
 * This will compress all images in the assets/images folder
 * 
 * IMPORTANT: Backup your images before running this!
 * Access at: http://localhost/dntoursahungalla/optimize_images.php
 * 
 * After running on Railway, DELETE THIS FILE for security!
 */

// Set execution time to unlimited for large batches
set_time_limit(0);
ini_set('memory_limit', '512M');

$imageFolder = __DIR__ . '/assets/images/';
$backupFolder = __DIR__ . '/assets/images_backup/';
$quality = 75; // JPEG quality (60-85 recommended)
$maxWidth = 1920; // Maximum width for images
$maxHeight = 1080; // Maximum height for images

// Create backup folder if it doesn't exist
if (!is_dir($backupFolder)) {
    mkdir($backupFolder, 0755, true);
}

$results = [
    'processed' => 0,
    'skipped' => 0,
    'errors' => 0,
    'total_saved' => 0,
    'details' => []
];

// Get all image files
$files = array_merge(
    glob($imageFolder . '*.jpg'),
    glob($imageFolder . '*.jpeg'),
    glob($imageFolder . '*.JPG'),
    glob($imageFolder . '*.JPEG'),
    glob($imageFolder . '*.png'),
    glob($imageFolder . '*.PNG')
);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Optimization - DN Tours</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #ff7e29;
            border-bottom: 3px solid #ff7e29;
            padding-bottom: 10px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-box {
            background: linear-gradient(135deg, #ff7e29, #ff9d4d);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .stat-box h3 {
            margin: 0;
            font-size: 2em;
        }
        .stat-box p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        .results {
            margin-top: 30px;
        }
        .result-item {
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
            background: #f9f9f9;
        }
        .result-item.error {
            border-left-color: #f44336;
            background: #ffebee;
        }
        .result-item.skipped {
            border-left-color: #ff9800;
            background: #fff3e0;
        }
        .filename {
            font-weight: bold;
            color: #333;
        }
        .size-info {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .savings {
            color: #4CAF50;
            font-weight: bold;
        }
        .button {
            background: linear-gradient(135deg, #ff7e29, #ff9d4d);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 10px 5px;
        }
        .button:hover {
            opacity: 0.9;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .progress {
            margin: 20px 0;
        }
        .progress-bar {
            width: 100%;
            height: 30px;
            background: #e0e0e0;
            border-radius: 15px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #ff7e29, #ff9d4d);
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖼️ Image Optimization Tool</h1>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['optimize'])): ?>
            
            <div class="warning">
                <strong>⚠️ Optimization in Progress...</strong><br>
                Please wait while we process your images. This may take a few minutes.
            </div>
            
            <div class="progress">
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 0%" id="progressBar"></div>
                </div>
            </div>
            
            <div class="results">
                <?php
                $totalFiles = count($files);
                $processed = 0;
                
                foreach ($files as $file) {
                    $filename = basename($file);
                    $originalSize = filesize($file);
                    
                    try {
                        // Create backup
                        $backupFile = $backupFolder . $filename;
                        if (!file_exists($backupFile)) {
                            copy($file, $backupFile);
                        }
                        
                        // Get image info
                        $info = getimagesize($file);
                        if ($info === false) {
                            throw new Exception("Not a valid image file");
                        }
                        
                        $mime = $info['mime'];
                        $width = $info[0];
                        $height = $info[1];
                        
                        // Skip if image is already small enough
                        if ($originalSize < 50000) { // 50KB
                            $results['skipped']++;
                            echo '<div class="result-item skipped">';
                            echo '<div class="filename">⊘ ' . htmlspecialchars($filename) . '</div>';
                            echo '<div class="size-info">Already optimized (' . number_format($originalSize/1024, 2) . ' KB)</div>';
                            echo '</div>';
                            flush();
                            ob_flush();
                            continue;
                        }
                        
                        // Load image based on type
                        if ($mime === 'image/jpeg') {
                            $image = imagecreatefromjpeg($file);
                        } elseif ($mime === 'image/png') {
                            $image = imagecreatefrompng($file);
                        } else {
                            throw new Exception("Unsupported image type: $mime");
                        }
                        
                        if ($image === false) {
                            throw new Exception("Failed to load image");
                        }
                        
                        // Resize if needed
                        if ($width > $maxWidth || $height > $maxHeight) {
                            $ratio = min($maxWidth / $width, $maxHeight / $height);
                            $newWidth = round($width * $ratio);
                            $newHeight = round($height * $ratio);
                            
                            $resized = imagecreatetruecolor($newWidth, $newHeight);
                            
                            // Preserve transparency for PNG
                            if ($mime === 'image/png') {
                                imagealphablending($resized, false);
                                imagesavealpha($resized, true);
                            }
                            
                            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                            imagedestroy($image);
                            $image = $resized;
                        }
                        
                        // Save optimized image
                        if ($mime === 'image/jpeg') {
                            imagejpeg($image, $file, $quality);
                        } elseif ($mime === 'image/png') {
                            imagepng($image, $file, 6);
                        }
                        
                        imagedestroy($image);
                        
                        $newSize = filesize($file);
                        $saved = $originalSize - $newSize;
                        $percent = round(($saved / $originalSize) * 100, 1);
                        
                        $results['processed']++;
                        $results['total_saved'] += $saved;
                        
                        echo '<div class="result-item">';
                        echo '<div class="filename">✓ ' . htmlspecialchars($filename) . '</div>';
                        echo '<div class="size-info">';
                        echo number_format($originalSize/1024, 2) . ' KB → ';
                        echo number_format($newSize/1024, 2) . ' KB ';
                        echo '<span class="savings">(-' . $percent . '%)</span>';
                        echo '</div>';
                        echo '</div>';
                        
                    } catch (Exception $e) {
                        $results['errors']++;
                        echo '<div class="result-item error">';
                        echo '<div class="filename">✗ ' . htmlspecialchars($filename) . '</div>';
                        echo '<div class="size-info">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
                        echo '</div>';
                    }
                    
                    $processed++;
                    $progress = round(($processed / $totalFiles) * 100);
                    echo "<script>document.getElementById('progressBar').style.width = '{$progress}%';</script>";
                    flush();
                    ob_flush();
                }
                ?>
            </div>
            
            <div class="stats">
                <div class="stat-box">
                    <h3><?php echo $results['processed']; ?></h3>
                    <p>Images Optimized</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $results['skipped']; ?></h3>
                    <p>Already Optimized</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo number_format($results['total_saved']/1024/1024, 2); ?> MB</h3>
                    <p>Total Space Saved</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo $results['errors']; ?></h3>
                    <p>Errors</p>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="index.php" class="button">← Back to Website</a>
                <a href="?clear_cache=1" class="button">Clear Cache & Test</a>
            </div>
            
        <?php else: ?>
            
            <div class="warning">
                <strong>⚠️ Important:</strong> This will optimize all images in <code>assets/images/</code> folder.<br>
                Original images will be backed up to <code>assets/images_backup/</code>
            </div>
            
            <div class="stats">
                <div class="stat-box">
                    <h3><?php echo count($files); ?></h3>
                    <p>Images Found</p>
                </div>
                <div class="stat-box">
                    <h3><?php echo number_format(array_sum(array_map('filesize', $files))/1024/1024, 2); ?> MB</h3>
                    <p>Total Current Size</p>
                </div>
            </div>
            
            <h2>Settings</h2>
            <ul>
                <li><strong>Quality:</strong> <?php echo $quality; ?>% (JPEG)</li>
                <li><strong>Max Width:</strong> <?php echo $maxWidth; ?>px</li>
                <li><strong>Max Height:</strong> <?php echo $maxHeight; ?>px</li>
                <li><strong>Backup Location:</strong> assets/images_backup/</li>
            </ul>
            
            <h2>What This Will Do:</h2>
            <ol>
                <li>✓ Backup all original images</li>
                <li>✓ Resize images larger than <?php echo $maxWidth; ?>x<?php echo $maxHeight; ?>px</li>
                <li>✓ Compress JPEG images to <?php echo $quality; ?>% quality</li>
                <li>✓ Optimize PNG compression</li>
                <li>✓ Skip images already under 50KB</li>
            </ol>
            
            <form method="POST" style="text-align: center; margin-top: 30px;">
                <button type="submit" name="optimize" class="button">
                    🚀 Start Optimization
                </button>
                <a href="index.php" class="button" style="background: #666;">Cancel</a>
            </form>
            
        <?php endif; ?>
    </div>
</body>
</html>

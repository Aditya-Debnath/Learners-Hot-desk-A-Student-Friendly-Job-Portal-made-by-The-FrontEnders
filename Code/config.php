<?php
// Start Output Buffering (Prevents Header Errors)
ob_start();

// Database Connection
$host = "localhost"; $user = "root"; $pass = ""; $db = "project_jobportal";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) { die("DB Error: " . $e->getMessage()); }

// Start Session
if (session_status() === PHP_SESSION_NONE) session_start();

// --- HELPER FUNCTIONS --- //

// 1. Check Login
function checkLogin() {
    if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }
}

// 2. Security Check (CSRF/XSS)
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// 3. Send Notification
function notify($pdo, $user_id, $msg, $link = '#') {
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $msg, $link]);
}

// 4. File Uploader
function uploadFile($file, $folder, $allowedTypes) {
    $fileName = time() . '_' . basename($file['name']);
    $target = "assets/$folder/" . $fileName;
    $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION));
    
    if(in_array($ext, $allowedTypes)) {
        if(move_uploaded_file($file['tmp_name'], $target)) return $fileName;
    }
    return false;
}
?>
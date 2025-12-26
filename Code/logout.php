<?php
// Include configuration to access session settings
require_once 'config.php';

// 1. Clear Session Data
$_SESSION = []; // Empty the session array

// 2. Destroy the Session
if (session_status() === PHP_SESSION_ACTIVE) {
    session_destroy();
}

// 3. Clear "Remember Me" Cookie (if it exists)
if (isset($_COOKIE['remember_token'])) {
    // Set the cookie expiration date to the past to delete it
    setcookie('remember_token', '', time() - 3600, '/');
}

// 4. Redirect to Login Page
header("Location: login.php");
exit;
?>
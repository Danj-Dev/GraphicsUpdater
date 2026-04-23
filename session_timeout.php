<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Timeout period in seconds (e.g., 10 minutes)
$timeout_duration = 60;

// Check for session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy(); 
    // Redirect with timeout message
    header("Location: index.php?message=" . urlencode("Your session has expired, please sign in again"));
    exit();
}

// Update last activity time
$_SESSION['LAST_ACTIVITY'] = time();


?>
<?php
/* =========================
   SESSION SECURITY
========================= */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

/* =========================
   DATABASE CONFIG
========================= */
define('DB_HOST', 'localhost');
define('DB_USER', 'ojesystems');
define('DB_PASS', '@Isaaco2009');
define('DB_NAME', 'secure');

/* =========================
   CONNECT TO DATABASE
========================= */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

/* =========================
   ERROR REPORTING
   (Disable in production)
========================= */
// Development
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Production (Hide errors)
error_reporting(0);
ini_set('display_errors', 0);

/* =========================
   HELPER FUNCTIONS
========================= */

/**
 * Require user to be logged in
 */
function requireLogin() {
    if (!isset($_SESSION['station_id'])) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Sanitize output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Generate random voucher code
 */
function generateVoucher($length = 6) {
    return substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, $length);
}

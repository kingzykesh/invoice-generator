<?php
// auth_config.php - Authentication configuration with direct database credentials
session_start();

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection details
$host = 'sdb-88.hosting.stackcp.net';
$dbname = 'invoicepoptimum-353131356978';
$username = 'invoicepoptimum-353131356978';
$password = 'vppklxvxm0';

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

// Password hashing function
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Verify password
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Helper function to get settings
function getSetting($key) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT $key FROM settings WHERE id = 1");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result[$key] ?? '';
    } catch (Exception $e) {
        return '';
    }
}

// Generate invoice number
function generateInvoiceNumber() {
    global $pdo;
    $prefix = "CB";
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM invoices");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result['count'] + 1;
        return $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    } catch (Exception $e) {
        return $prefix . '-001';
    }
}
?>
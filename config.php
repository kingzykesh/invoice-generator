<?php
// config.php - Database connection and authentication
session_start();

$host = 'sdb-88.hosting.stackcp.net';
$dbname = 'invoicepoptimum-353131356978';
$username = 'invoicepoptimum-353131356978';
$password = 'vppklxvxm0';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

// Helper functions
function getSetting($pdo, $key) {
    $stmt = $pdo->prepare("SELECT $key FROM settings WHERE id = 1");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result[$key] ?? '';
}

function generateInvoiceNumber($pdo) {
    $prefix = "CB";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM invoices");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'] + 1;
    return $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
}
?>
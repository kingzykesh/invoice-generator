<?php
// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Database connection details
$host = 'sdb-88.hosting.stackcp.net';
$dbname = 'invoicepoptimum-353131356978';
$username = 'invoicepoptimum-353131356978';
$password = 'vppklxvxm0';

// Try to connect to database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<!-- Database connection successful -->";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if users table exists
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<!-- Users table exists -->";
} catch (Exception $e) {
    die("Users table error: " . $e->getMessage());
}

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Check if user is logged in
if (isLoggedIn()) {
    echo "<!-- User is logged in -->";
} else {
    echo "<!-- User is not logged in -->";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Management System - Debug Mode</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#dc2626',
                        secondary: '#fef2f2'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">
    <header class="bg-primary text-white shadow-lg">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-file-invoice text-3xl"></i>
                    <h1 class="text-2xl font-bold">Invoice Management System - DEBUG MODE</h1>
                </div>
                <div class="text-sm">
                    <?php echo date('l, F j, Y'); ?>
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold text-primary mb-4">Debug Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-secondary p-4 rounded-lg">
                    <h3 class="font-medium mb-2">Session Status</h3>
                    <p class="text-sm">
                        <?php echo isLoggedIn() ? 'Logged In' : 'Not Logged In'; ?>
                    </p>
                    <?php if (isLoggedIn()): ?>
                    <p class="text-sm">User ID: <?php echo $_SESSION['user_id']; ?></p>
                    <p class="text-sm">Username: <?php echo $_SESSION['username']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="bg-secondary p-4 rounded-lg">
                    <h3 class="font-medium mb-2">Database Status</h3>
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
                        $users = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM invoices");
                        $invoices = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
                        $products = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        echo "<p class='text-sm'>Users: {$users['count']}</p>";
                        echo "<p class='text-sm'>Invoices: {$invoices['count']}</p>";
                        echo "<p class='text-sm'>Products: {$products['count']}</p>";
                    } catch (Exception $e) {
                        echo "<p class='text-sm text-red-600'>Error: " . $e->getMessage() . "</p>";
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Products Card -->
            <a href="products.php" class="block">
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border-l-4 border-primary">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Products</h2>
                                <p class="text-gray-600 mt-2">Manage your product inventory</p>
                            </div>
                            <div class="bg-secondary p-4 rounded-full">
                                <i class="fas fa-boxes text-primary text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-6">
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo "<span class='text-3xl font-bold text-primary'>{$result['count']}</span>";
                            } catch (Exception $e) {
                                echo "<span class='text-red-600'>Error</span>";
                            }
                            ?>
                            <span class="text-gray-600 ml-2">products</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Create Invoice Card -->
            <a href="purchase.php" class="block">
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border-l-4 border-primary">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Create Invoice</h2>
                                <p class="text-gray-600 mt-2">Generate new sales invoices</p>
                            </div>
                            <div class="bg-secondary p-4 rounded-full">
                                <i class="fas fa-file-invoice-dollar text-primary text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-6">
                            <span class="bg-primary text-white px-3 py-1 rounded-full text-sm font-medium">New</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Invoice Tracking Card -->
            <a href="tracking.php" class="block">
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border-l-4 border-primary">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Invoice Tracking</h2>
                                <p class="text-gray-600 mt-2">View and search all invoices</p>
                            </div>
                            <div class="bg-secondary p-4 rounded-full">
                                <i class="fas fa-search-dollar text-primary text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-6">
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT COUNT(*) as count FROM invoices");
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo "<span class='text-3xl font-bold text-primary'>{$result['count']}</span>";
                            } catch (Exception $e) {
                                echo "<span class='text-red-600'>Error</span>";
                            }
                            ?>
                            <span class="text-gray-600 ml-2">invoices</span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Settings Card -->
            <a href="settings.php" class="block">
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border-l-4 border-primary">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Settings</h2>
                                <p class="text-gray-600 mt-2">Configure system preferences</p>
                            </div>
                            <div class="bg-secondary p-4 rounded-full">
                                <i class="fas fa-cog text-primary text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-6">
                            <span class="text-gray-600">Company: 
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT company_name FROM settings WHERE id = 1");
                                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                echo htmlspecialchars($result['company_name'] ?? 'Not set');
                            } catch (Exception $e) {
                                echo 'Error';
                            }
                            ?>
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Login Card -->
            <?php if (!isLoggedIn()): ?>
            <a href="login.php" class="block">
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border-l-4 border-primary">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Login</h2>
                                <p class="text-gray-600 mt-2">Access your account</p>
                            </div>
                            <div class="bg-secondary p-4 rounded-full">
                                <i class="fas fa-sign-in-alt text-primary text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-6">
                            <span class="bg-primary text-white px-3 py-1 rounded-full text-sm font-medium">Access</span>
                        </div>
                    </div>
                </div>
            </a>
            <?php else: ?>
            <a href="logout.php" class="block">
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border-l-4 border-primary">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Logout</h2>
                                <p class="text-gray-600 mt-2">Exit the system</p>
                            </div>
                            <div class="bg-secondary p-4 rounded-full">
                                <i class="fas fa-sign-out-alt text-primary text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-6">
                            <span class="text-gray-600">User: <?php echo $_SESSION['username']; ?></span>
                        </div>
                    </div>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </main>

    <footer class="bg-primary text-white mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-lg font-semibold">Invoice Management System</h3>
                    <p class="text-secondary">Debug Mode - Troubleshooting white page issue</p>
                </div>
                <div class="text-sm">
                    <p>PHP Version: <?php echo phpversion(); ?></p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
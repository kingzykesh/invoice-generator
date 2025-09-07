<?php
// index.php - Dashboard
require_once 'auth_config.php';
requireLogin();

// Check if settings exist, if not redirect to settings page
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM settings");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] == 0) {
        header("Location: settings.php");
        exit();
    }
} catch (Exception $e) {
    // If settings table doesn't exist yet, we'll handle it gracefully
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Management System</title>
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
    <?php include 'navbar.php'; ?>

    <main class="container mx-auto px-4 py-8">
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
                                $company_name = getSetting('company_name');
                                echo htmlspecialchars($company_name ?: 'Not set');
                            } catch (Exception $e) {
                                echo 'Error loading settings';
                            }
                            ?>
                            </span>
                        </div>
                    </div>
                </div>
            </a>

            <!-- Logout Card -->
            <a href="logout.php" class="block">
                <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden border-l-4 border-primary">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">Logout</h2>
                                <p class="text-gray-600 mt-2">Exit the system securely</p>
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

            <!-- Reports Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-gray-300">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Reports</h2>
                            <p class="text-gray-600 mt-2">Sales and financial reports</p>
                        </div>
                        <div class="bg-gray-100 p-4 rounded-full">
                            <i class="fas fa-chart-bar text-gray-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="mt-6">
                        <span class="text-gray-400">Coming soon</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Invoices Section -->
        <div class="mt-12 bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Recent Invoices</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        try {
                            $stmt = $pdo->query("SELECT * FROM invoices ORDER BY date_issued DESC LIMIT 5");
                            $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            
                            if (count($invoices) > 0) {
                                foreach ($invoices as $invoice) {
                                    $status = strtotime($invoice['due_date']) < time() ? 'Overdue' : 'Pending';
                                    $statusColor = $status === 'Overdue' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800';
                                    
                                    // Extract customer name from bill_to (first line)
                                    $billToLines = explode("\n", $invoice['bill_to']);
                                    $customerName = !empty($billToLines) ? $billToLines[0] : 'Unknown';
                                    
                                    $currency = getSetting('currency') ?: '₦';
                                    
                                    echo "
                                    <tr>
                                        <td class='px-6 py-4 whitespace-nowrap'>
                                            <a href='print_invoice.php?id={$invoice['id']}' class='text-primary hover:underline'>{$invoice['invoice_number']}</a>
                                        </td>
                                        <td class='px-6 py-4 whitespace-nowrap'>{$invoice['date_issued']}</td>
                                        <td class='px-6 py-4 whitespace-nowrap'>" . htmlspecialchars($customerName) . "</td>
                                        <td class='px-6 py-4 whitespace-nowrap'>{$currency}" . number_format($invoice['grand_total'], 2) . "</td>
                                        <td class='px-6 py-4 whitespace-nowrap'>
                                            <span class='px-2 inline-flex text-xs leading-5 font-semibold rounded-full {$statusColor}'>
                                                {$status}
                                            </span>
                                        </td>
                                    </tr>
                                    ";
                                }
                            } else {
                                echo "
                                <tr>
                                    <td colspan='5' class='px-6 py-4 text-center text-gray-500'>
                                        No invoices found. <a href='purchase.php' class='text-primary hover:underline'>Create your first invoice</a>
                                    </td>
                                </tr>
                                ";
                            }
                        } catch (Exception $e) {
                            echo "
                            <tr>
                                <td colspan='5' class='px-6 py-4 text-center text-red-500'>
                                    Error loading invoices: " . htmlspecialchars($e->getMessage()) . "
                                </td>
                            </tr>
                            ";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50">
                <a href="tracking.php" class="text-primary hover:underline font-medium">View all invoices →</a>
            </div>
        </div>
    </main>

    <footer class="bg-primary text-white mt-12">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-lg font-semibold">Invoice Management System</h3>
                    <p class="text-secondary">Streamlining your billing process</p>
                </div>
                <div class="text-sm">
                    <p>&copy; <?php echo date('Y'); ?> 
                    <?php
                    try {
                        $company_name = getSetting('company_name');
                        echo htmlspecialchars($company_name ?: 'Invoice System');
                    } catch (Exception $e) {
                        echo 'Invoice System';
                    }
                    ?>. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
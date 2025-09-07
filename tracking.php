<?php
// tracking.php - Invoice tracking
require_once 'auth_config.php';
requireLogin();

// Search functionality
$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM invoices WHERE invoice_number LIKE ? OR date_issued LIKE ? OR due_date LIKE ? ORDER BY date_issued DESC");
        $searchTerm = "%$search%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Search error: " . $e->getMessage();
        $invoices = [];
    }
} else {
    try {
        $stmt = $pdo->query("SELECT * FROM invoices ORDER BY date_issued DESC");
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $error = "Error loading invoices: " . $e->getMessage();
        $invoices = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Tracking - PINVOICE GENERATOR</title>
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
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Invoice Tracking</h1>
            <p class="text-gray-600">View and manage all your invoices</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <form method="GET">
                        <div class="relative">
                            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary" 
                                placeholder="Search by invoice number or date...">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </form>
                </div>
                <div>
                    <a href="purchase.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-red-700 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> New Invoice
                    </a>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Issued</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">P.O. #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Discount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grand Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (count($invoices) > 0): ?>
                            <?php foreach ($invoices as $invoice): 
                                $currency = getSetting('currency') ?: '₦';
                                $status = strtotime($invoice['due_date']) < time() ? 'Overdue' : 'Pending';
                                $statusColor = $status === 'Overdue' ? 'text-red-600' : 'text-green-600';
                            ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $invoice['invoice_number']; ?></div>
                                    <div class="text-sm text-gray-500 <?php echo $statusColor; ?>"><?php echo $status; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $invoice['date_issued']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $invoice['due_date']; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $invoice['po_number'] ?: 'N/A'; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $currency . number_format($invoice['subtotal'], 2); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $currency . number_format($invoice['discount'], 2); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $currency . number_format($invoice['grand_total'], 2); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="print_invoice.php?id=<?php echo $invoice['id']; ?>" class="text-primary hover:text-red-700 mr-3">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </a>
                                    <?php if (isAdmin()): ?>
                                    <a href="#" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    No invoices found. <a href="purchase.php" class="text-primary hover:underline">Create your first invoice</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
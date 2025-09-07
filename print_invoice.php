<?php
// print_invoice.php - Invoice printing
require_once 'auth_config.php';
requireLogin();

if (!isset($_GET['id'])) {
    header("Location: tracking.php");
    exit();
}

$invoice_id = $_GET['id'];

// Get invoice details
try {
    $stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
    $stmt->execute([$invoice_id]);
    $invoice = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$invoice) {
        header("Location: tracking.php");
        exit();
    }
    
    // Get invoice items
    $stmt = $pdo->prepare("
        SELECT ii.*, p.name, p.description 
        FROM invoice_items ii 
        JOIN products p ON ii.product_id = p.id 
        WHERE ii.invoice_id = ?
    ");
    $stmt->execute([$invoice_id]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get company settings
    $company_name = getSetting('company_name');
    $company_address = getSetting('company_address');
    $company_phone = getSetting('company_phone');
    $company_fax = getSetting('company_fax');
    $company_website = getSetting('company_website');
    $company_email = getSetting('company_email');
    $currency = getSetting('currency') ?: '₦';
    
} catch (Exception $e) {
    die("Error loading invoice: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?php echo $invoice['invoice_number']; ?> - PINVOICE GENERATOR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; background: white; }
            .invoice-container { border: none; box-shadow: none; margin: 0; padding: 0; }
            .invoice { border: none; padding: 0; }
            .print-only { display: block !important; }
        }
        .print-only { display: none; }
        body { font-family: Arial, sans-serif; background: #f3f4f6; }
        .invoice { border: 1px solid #ddd; padding: 30px; background: white; max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #dc2626; padding-bottom: 20px; }
        .company-info { margin-bottom: 30px; }
        .billing-shipping { display: flex; justify-content: space-between; margin-bottom: 30px; gap: 20px; }
        .billing-shipping > div { flex: 1; }
        .invoice-details { margin-bottom: 30px; background: #f9fafb; padding: 20px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { padding: 12px; text-align: left; border: 1px solid #e5e7eb; }
        th { background-color: #f8fafc; font-weight: 600; }
        .text-right { text-align: right; }
        .footer { margin-top: 50px; text-align: center; padding-top: 20px; border-top: 1px solid #e5e7eb; }
        .total-row { font-weight: bold; background-color: #fef2f2; }
    </style>
</head>
<body class="bg-black">
    <?php include 'navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Action buttons - hidden when printing -->
        <div class="no-print mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Invoice <?php echo $invoice['invoice_number']; ?></h1>
                <p class="text-gray-600">Preview and print your invoice</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <i class="fas fa-print mr-2"></i> Print Invoice
                </button>
                <a href="tracking.php" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Invoices
                </a>
                <a href="purchase.php" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <i class="fas fa-plus mr-2"></i> New Invoice
                </a>
            </div>
        </div>

        <!-- Invoice container - different styling for print -->
        <div class="invoice-container bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="invoice">
                <div class="header">
                    <h1 class="text-3xl font-bold text-primary">SALES INVOICE</h1>
                    <div class="print-only mt-4">
                        <h2 class="text-xl font-semibold"><?php echo htmlspecialchars($company_name); ?></h2>
                        <p><?php echo nl2br(htmlspecialchars($company_address)); ?></p>
                        <p>Phone: <?php echo htmlspecialchars($company_phone); ?> | Fax: <?php echo htmlspecialchars($company_fax); ?></p>
                    </div>
                </div>
                
                <div class="company-info no-print">
                    <h2 class="text-xl font-semibold text-gray-800"><?php echo htmlspecialchars($company_name); ?></h2>
                    <p class="text-gray-600"><?php echo nl2br(htmlspecialchars($company_address)); ?></p>
                    <p class="text-gray-600">Phone: <?php echo htmlspecialchars($company_phone); ?> | Fax: <?php echo htmlspecialchars($company_fax); ?></p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($company_website); ?></p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($company_email); ?></p>
                </div>
                
                <div class="billing-shipping">
                    <div class="bill-to">
                        <h3 class="font-semibold text-lg text-gray-800 mb-2">BILL TO</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($invoice['bill_to'])); ?></p>
                        </div>
                    </div>
                    <div class="ship-to">
                        <h3 class="font-semibold text-lg text-gray-800 mb-2">SHIP TO</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($invoice['ship_to'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="invoice-details">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-gray-700"><strong class="text-gray-800">Invoice #:</strong> <?php echo htmlspecialchars($invoice['invoice_number']); ?></p>
                            <p class="text-gray-700"><strong class="text-gray-800">Date of Purchase:</strong> <?php echo htmlspecialchars($invoice['date_issued']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-700"><strong class="text-gray-800">P.O. #:</strong> <?php echo htmlspecialchars($invoice['po_number'] ?: 'N/A'); ?></p>
                            <p class="text-gray-700"><strong class="text-gray-800">Due Date:</strong> <?php echo htmlspecialchars($invoice['due_date']); ?></p>
                        </div>
                    </div>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>ITEM</th>
                            <th>QUANTITY</th>
                            <th>UNIT PRICE</th>
                            <th>AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="font-medium"><?php echo htmlspecialchars($item['name']); ?></div>
                                <?php if (!empty($item['description'])): ?>
                                <div class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($item['description']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                            <td><?php echo $currency . number_format($item['unit_price'], 2); ?></td>
                            <td><?php echo $currency . number_format($item['amount'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <tr class="total-row">
                            <td colspan="2" rowspan="3"></td>
                            <td>SUB-TOTAL</td>
                            <td><?php echo $currency . number_format($invoice['subtotal'], 2); ?></td>
                        </tr>
                        <tr class="total-row">
                            <td>DISCOUNT <?php echo number_format(($invoice['discount'] / $invoice['subtotal']) * 100, 0); ?>%</td>
                            <td>-<?php echo $currency . number_format($invoice['discount'], 2); ?></td>
                        </tr>
                        <tr class="total-row">
                            <td>GRAND TOTAL</td>
                            <td><strong><?php echo $currency . number_format($invoice['grand_total'], 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="footer">
                    <p class="text-gray-700">Cheques should be made payable to <strong><?php echo htmlspecialchars($company_name); ?></strong></p>
                    <p class="text-gray-600 mt-2">Get a 10% off with the next purchase with discount code: <strong class="text-primary"><?php echo htmlspecialchars($invoice['discount_code']); ?></strong></p>
                </div>
            </div>
        </div>

        <!-- Print button at bottom - hidden when printing -->
        <div class="no-print mt-6 text-center">
            <button onclick="window.print()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors inline-flex items-center">
                <i class="fas fa-print mr-2"></i> Print Invoice
            </button>
        </div>
    </div>

    <script>
    // Enhance print functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add print event listener
        const printButtons = document.querySelectorAll('button[onclick="window.print()"]');
        printButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Add a small delay to ensure the print dialog appears after the page is fully rendered
                setTimeout(function() {
                    window.print();
                }, 100);
            });
        });
    });
    </script>
</body>
</html>
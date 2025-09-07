<?php
// purchase.php - Invoice creation page
require_once 'auth_config.php';
requireLogin();

// Get all products for dropdown
try {
    $products_stmt = $pdo->query("SELECT * FROM products ORDER BY name");
    $products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error loading products: " . $e->getMessage();
    $products = [];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_invoice'])) {
    try {
        // Save invoice to database
        $invoice_number = generateInvoiceNumber();
        $date_issued = $_POST['date_issued'];
        $due_date = $_POST['due_date'];
        $po_number = $_POST['po_number'];
        $bill_to = $_POST['bill_to'];
        $ship_to = $_POST['ship_to'];
        $subtotal = $_POST['subtotal'];
        $discount = $_POST['discount'];
        $grand_total = $_POST['grand_total'];
        $discount_code = "DOGE" . rand(1000, 9999);
        
        // Insert invoice
        $stmt = $pdo->prepare("INSERT INTO invoices (invoice_number, date_issued, due_date, po_number, bill_to, ship_to, subtotal, discount, grand_total, discount_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$invoice_number, $date_issued, $due_date, $po_number, $bill_to, $ship_to, $subtotal, $discount, $grand_total, $discount_code]);
        $invoice_id = $pdo->lastInsertId();
        
        // Insert invoice items
        $product_ids = $_POST['product_id'];
        $quantities = $_POST['quantity'];
        $unit_prices = $_POST['unit_price'];
        $amounts = $_POST['amount'];
        
        for ($i = 0; $i < count($product_ids); $i++) {
            if (!empty($product_ids[$i]) && !empty($quantities[$i])) {
                $stmt = $pdo->prepare("INSERT INTO invoice_items (invoice_id, product_id, quantity, unit_price, amount) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$invoice_id, $product_ids[$i], $quantities[$i], $unit_prices[$i], $amounts[$i]]);
            }
        }
        
        // Redirect to print page
        header("Location: print_invoice.php?id=" . $invoice_id);
        exit();
    } catch (Exception $e) {
        $error = "Error creating invoice: " . $e->getMessage();
    }
}

// Get company address for bill_to/ship_to defaults
try {
    $company_address = getSetting('company_address');
} catch (Exception $e) {
    $company_address = '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice - PINVOICE GENERATOR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <h1 class="text-3xl font-bold text-gray-800">Create New Invoice</h1>
            <p class="text-gray-600">Generate professional invoices for your customers</p>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="invoiceForm" class="bg-white rounded-lg shadow-md p-6">
            <!-- Bill To / Ship To Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Bill To</h3>
                    <textarea name="bill_to" rows="4" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"><?php echo htmlspecialchars($company_address); ?></textarea>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Ship To</h3>
                    <textarea name="ship_to" rows="4" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"><?php echo htmlspecialchars($company_address); ?></textarea>
                </div>
            </div>
            
            <!-- Invoice Details -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Invoice #</label>
                    <input type="text" value="<?php echo generateInvoiceNumber(); ?>" disabled 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                    <input type="hidden" name="invoice_number" value="<?php echo generateInvoiceNumber(); ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Purchase</label>
                    <input type="date" name="date_issued" value="<?php echo date('Y-m-d'); ?>" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">P.O. #</label>
                    <input type="text" name="po_number" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                    <input type="date" name="due_date" value="<?php echo date('Y-m-d', strtotime('+30 days')); ?>" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
            </div>
            
            <!-- Items Table -->
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Items</h3>
            <table class="w-full mb-8" id="itemsTable">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Item</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Quantity</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Unit Price</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Amount</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 py-2">
                            <select class="w-full product-select border border-gray-300 rounded-md px-2 py-1" name="product_id[]">
                                <option value="">Select Product</option>
                                <?php foreach ($products as $product): ?>
                                <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['cost_price']; ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" class="quantity w-full border border-gray-300 rounded-md px-2 py-1" name="quantity[]" min="1" value="1">
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" step="0.01" class="unit-price w-full border border-gray-300 rounded-md px-2 py-1 bg-gray-50" name="unit_price[]" readonly>
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" step="0.01" class="amount w-full border border-gray-300 rounded-md px-2 py-1 bg-gray-50" name="amount[]" readonly>
                        </td>
                        <td class="px-4 py-2">
                            <button type="button" class="remove-row text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="px-4 py-2">
                            <button type="button" class="bg-primary text-white px-3 py-1 rounded-md hover:bg-red-700" id="addRow">
                                <i class="fas fa-plus mr-1"></i> Add Item
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td class="px-4 py-2 font-medium">SUB-TOTAL</td>
                        <td class="px-4 py-2">
                            <input type="number" step="0.01" class="w-full border border-gray-300 rounded-md px-2 py-1 bg-gray-50" name="subtotal" id="subtotal" readonly>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td class="px-4 py-2 font-medium">
                            DISCOUNT <input type="number" step="0.01" class="w-20 border border-gray-300 rounded-md px-2 py-1 ml-1" name="discount_percent" id="discountPercent" value="0">%
                        </td>
                        <td class="px-4 py-2">
                            <input type="number" step="0.01" class="w-full border border-gray-300 rounded-md px-2 py-1 bg-gray-50" name="discount" id="discount" readonly>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td class="px-4 py-2 font-medium">GRAND TOTAL</td>
                        <td class="px-4 py-2">
                            <input type="number" step="0.01" class="w-full border border-gray-300 rounded-md px-2 py-1 bg-gray-50" name="grand_total" id="grandTotal" readonly>
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            
            <div class="flex justify-end">
                <button type="submit" name="create_invoice" class="bg-primary text-white px-6 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-primary">
                    <i class="fas fa-save mr-2"></i> Save and Print Invoice
                </button>
            </div>
        </form>
    </main>

    <script>
    $(document).ready(function() {
        // Add new row
        $('#addRow').click(function() {
            var newRow = $('#itemsTable tbody tr:first').clone();
            newRow.find('input').val('');
            newRow.find('.product-select').val('');
            $('#itemsTable tbody').append(newRow);
        });
        
        // Remove row
        $(document).on('click', '.remove-row', function() {
            if ($('#itemsTable tbody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
            }
        });
        
        // Product selection change
        $(document).on('change', '.product-select', function() {
            var selectedOption = $(this).find('option:selected');
            var unitPrice = selectedOption.data('price');
            $(this).closest('tr').find('.unit-price').val(unitPrice);
            calculateRowTotal($(this).closest('tr'));
        });
        
        // Quantity change
        $(document).on('input', '.quantity', function() {
            calculateRowTotal($(this).closest('tr'));
        });
        
        // Discount percentage change
        $('#discountPercent').on('input', function() {
            calculateTotals();
        });
        
        // Calculate row total
        function calculateRowTotal(row) {
            var quantity = parseFloat(row.find('.quantity').val()) || 0;
            var unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
            var amount = quantity * unitPrice;
            row.find('.amount').val(amount.toFixed(2));
            calculateTotals();
        }
        
        // Calculate all totals
        function calculateTotals() {
            var subtotal = 0;
            $('.amount').each(function() {
                subtotal += parseFloat($(this).val()) || 0;
            });
            
            $('#subtotal').val(subtotal.toFixed(2));
            
            var discountPercent = parseFloat($('#discountPercent').val()) || 0;
            var discount = subtotal * (discountPercent / 100);
            $('#discount').val(discount.toFixed(2));
            
            var grandTotal = subtotal - discount;
            $('#grandTotal').val(grandTotal.toFixed(2));
        }
    });
    </script>
</body>
</html>
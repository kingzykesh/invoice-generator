<?php
// products.php - Product management
require_once 'auth_config.php';
requireLogin();
requireAdmin(); // Only admins can manage products

// Add new product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $cost_price = $_POST['cost_price'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, description, cost_price) VALUES (?, ?, ?)");
        $stmt->execute([$name, $description, $cost_price]);
        $success = "Product added successfully!";
    } catch (Exception $e) {
        $error = "Error adding product: " . $e->getMessage();
    }
}

// Get all products
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY name");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error = "Error loading products: " . $e->getMessage();
    $products = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - PINVOICE GENERATOR</title>
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
            <h1 class="text-3xl font-bold text-gray-800">Product Management</h1>
            <p class="text-gray-600">Manage your product inventory</p>
        </div>
        
        <?php if (isset($success)): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo $success; ?></span>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Add Product Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Add New Product</h2>
                <form method="POST">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Product Name</label>
                        <input type="text" id="name" name="name" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">Description</label>
                        <textarea id="description" name="description" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary" 
                            rows="3"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="cost_price">Cost Price</label>
                        <input type="number" step="0.01" id="cost_price" name="cost_price" required 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <button type="submit" name="add_product" 
                        class="bg-primary text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-primary">
                        Add Product
                    </button>
                </form>
            </div>
            
            <!-- Existing Products -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Existing Products</h2>
                <?php if (count($products) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Name</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Description</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-2 text-sm text-gray-800"><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-600"><?php echo htmlspecialchars($product['description']); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-800">
                                        <?php echo getSetting('currency') . number_format($product['cost_price'], 2); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">No products found. Add your first product using the form.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
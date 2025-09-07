<?php
// settings.php - System settings
require_once 'auth_config.php';
requireLogin();
requireAdmin(); // Only admins can change settings

// Update settings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $company_name = $_POST['company_name'];
    $company_address = $_POST['company_address'];
    $company_phone = $_POST['company_phone'];
    $company_fax = $_POST['company_fax'];
    $company_website = $_POST['company_website'];
    $company_email = $_POST['company_email'];
    $currency = $_POST['currency'];
    
    try {
        // Check if settings already exist
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM settings");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            // Update existing settings
            $stmt = $pdo->prepare("UPDATE settings SET company_name = ?, company_address = ?, company_phone = ?, company_fax = ?, company_website = ?, company_email = ?, currency = ? WHERE id = 1");
            $stmt->execute([$company_name, $company_address, $company_phone, $company_fax, $company_website, $company_email, $currency]);
        } else {
            // Insert new settings
            $stmt = $pdo->prepare("INSERT INTO settings (company_name, company_address, company_phone, company_fax, company_website, company_email, currency) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$company_name, $company_address, $company_phone, $company_fax, $company_website, $company_email, $currency]);
        }
        
        $success = "Settings updated successfully!";
    } catch (Exception $e) {
        $error = "Error updating settings: " . $e->getMessage();
    }
}

// Get current settings
try {
    $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    $error = "Error loading settings: " . $e->getMessage();
    $settings = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - PINVOICE GENERATOR</title>
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
            <h1 class="text-3xl font-bold text-gray-800">System Settings</h1>
            <p class="text-gray-600">Configure your company information and system preferences</p>
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
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-primary text-white px-6 py-4">
                <h2 class="text-xl font-bold">Company Information</h2>
            </div>
            
            <div class="p-6">
                <form method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="company_name">Company Name *</label>
                            <input type="text" id="company_name" name="company_name" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                value="<?php echo htmlspecialchars($settings['company_name'] ?? ''); ?>">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="currency">Currency *</label>
                            <select id="currency" name="currency" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                                <option value="₦" <?php echo (($settings['currency'] ?? '₦') === '₦') ? 'selected' : ''; ?>>Naira (₦)</option>
                                <option value="$" <?php echo (($settings['currency'] ?? '₦') === '$') ? 'selected' : ''; ?>>Dollar ($)</option>
                                <option value="€" <?php echo (($settings['currency'] ?? '₦') === '€') ? 'selected' : ''; ?>>Euro (€)</option>
                                <option value="£" <?php echo (($settings['currency'] ?? '₦') === '£') ? 'selected' : ''; ?>>Pound (£)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="company_address">Company Address *</label>
                        <textarea id="company_address" name="company_address" required rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"><?php echo htmlspecialchars($settings['company_address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="company_phone">Phone</label>
                            <input type="text" id="company_phone" name="company_phone" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                value="<?php echo htmlspecialchars($settings['company_phone'] ?? ''); ?>">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="company_fax">Fax</label>
                            <input type="text" id="company_fax" name="company_fax" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                value="<?php echo htmlspecialchars($settings['company_fax'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="company_website">Website</label>
                            <input type="url" id="company_website" name="company_website" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                value="<?php echo htmlspecialchars($settings['company_website'] ?? ''); ?>">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="company_email">Email</label>
                            <input type="email" id="company_email" name="company_email" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary"
                                value="<?php echo htmlspecialchars($settings['company_email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-primary">
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
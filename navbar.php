<?php
// navbar.php - Navigation bar component
require_once 'auth_config.php';
?>

<nav class="bg-primary text-white shadow-lg">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <!-- Logo and Brand -->
            <div class="flex items-center space-x-4">
                <a href="index.php" class="flex items-center space-x-2">
                    <i class="fas fa-file-invoice text-2xl"></i>
                    <span class="text-xl font-bold">PINVOICE GENERATOR</span>
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-home mr-1"></i> Dashboard
                </a>
                
                <?php if (isAdmin()): ?>
                <a href="products.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-boxes mr-1"></i> Products
                </a>
                <?php endif; ?>
                
                <a href="purchase.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'purchase.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-file-invoice-dollar mr-1"></i> Create Invoice
                </a>
                
                <a href="tracking.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'tracking.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-search-dollar mr-1"></i> Invoices
                </a>
                
                <?php if (isAdmin()): ?>
                <a href="settings.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-cog mr-1"></i> Settings
                </a>
                
                <a href="register_user.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'register_user.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-user-plus mr-1"></i> Add User
                </a>
                <?php endif; ?>
            </div>

            <!-- User Menu -->
            <div class="flex items-center space-x-4">
                <div class="hidden md:block text-sm">
                    Welcome, <span class="font-medium"><?php echo $_SESSION['username']; ?></span>
                    <?php if (isAdmin()): ?>
                        <span class="bg-secondary text-primary text-xs px-2 py-1 rounded-full ml-2">Admin</span>
                    <?php endif; ?>
                </div>
                <a href="logout.php" class="bg-white text-primary px-4 py-2 rounded-lg hover:bg-secondary transition-colors duration-300">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
                
                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <div class="flex flex-col space-y-3">
                <a href="index.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
                
                <?php if (isAdmin()): ?>
                <a href="products.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-boxes mr-2"></i> Products
                </a>
                <?php endif; ?>
                
                <a href="purchase.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'purchase.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-file-invoice-dollar mr-2"></i> Create Invoice
                </a>
                
                <a href="tracking.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'tracking.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-search-dollar mr-2"></i> Invoices
                </a>
                
                <?php if (isAdmin()): ?>
                <a href="settings.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-cog mr-2"></i> Settings
                </a>
                
                <a href="register_user.php" class="hover:text-secondary transition-colors duration-300 <?php echo basename($_SERVER['PHP_SELF']) == 'register_user.php' ? 'text-secondary font-medium' : ''; ?>">
                    <i class="fas fa-user-plus mr-2"></i> Add User
                </a>
                <?php endif; ?>
                
                <div class="pt-3 border-t border-white border-opacity-20">
                    <div class="text-sm">
                        Logged in as: <span class="font-medium"><?php echo $_SESSION['username']; ?></span>
                        <?php if (isAdmin()): ?>
                            <span class="bg-secondary text-primary text-xs px-2 py-1 rounded-full ml-2">Admin</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        menu.classList.toggle('hidden');
    });
</script>
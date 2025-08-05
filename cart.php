<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$cart = new Cart();
$cartItems = $cart->getItems($_SESSION['user_id']);
$total = 0;

$error = '';
$success = '';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } else {
        switch ($action) {
            case 'update_quantity':
                $item_id = intval($_POST['item_id'] ?? 0);
                $quantity = intval($_POST['quantity'] ?? 1);
                
                if ($quantity > 0 && $quantity <= 10) {
                    $cart->updateQuantity($item_id, $quantity);
                    $success = 'Cart updated successfully!';
                } else {
                    $error = 'Invalid quantity.';
                }
                break;
                
            case 'remove_item':
                $item_id = intval($_POST['item_id'] ?? 0);
                $cart->removeItem($item_id);
                $success = 'Item removed from cart!';
                break;
                
            case 'clear_cart':
                $cart->clearCart($_SESSION['user_id']);
                $success = 'Cart cleared successfully!';
                break;
        }
        
        // Refresh cart items
        $cartItems = $cart->getItems($_SESSION['user_id']);
    }
}

// Calculate total
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Dream Wear</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#dc2626',
                        accent: '#f59e0b'
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-2">
                        <img src="logo.jpg" alt="Dream Wear" class="h-8 w-auto">
                        <span class="text-xl font-bold text-gray-900">Dream Wear</span>
                    </a>
                </div>

                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                    <a href="products.php" class="text-gray-700 hover:text-primary transition-colors">Products</a>
                    <a href="about.php" class="text-gray-700 hover:text-primary transition-colors">About</a>
                    <a href="contact.php" class="text-gray-700 hover:text-primary transition-colors">Contact</a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="relative text-primary font-semibold">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center"><?php echo count($cartItems); ?></span>
                    </a>
                    
                                                <div class="relative" id="user-dropdown">
                                <button id="user-dropdown-btn" class="flex items-center space-x-2 text-gray-700 hover:text-primary transition-colors">
                                    <i class="fas fa-user-circle text-xl"></i>
                                    <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                                    <i class="fas fa-chevron-down text-sm"></i>
                                </button>
                                <div id="user-dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                    <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                    <a href="orders.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                                </div>
                            </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Shopping Cart</h1>
            <p class="text-gray-600">Review and manage your items</p>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
        </div>
        <?php endif; ?>

        <?php if (empty($cartItems)): ?>
        <!-- Empty Cart -->
        <div class="text-center py-12">
            <i class="fas fa-shopping-cart text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Your cart is empty</h3>
            <p class="text-gray-500 mb-6">Looks like you haven't added any items to your cart yet.</p>
            <a href="products.php" class="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Start Shopping
            </a>
        </div>
        <?php else: ?>
        <!-- Cart Items -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold text-gray-900">Cart Items (<?php echo count($cartItems); ?>)</h2>
                    </div>
                    
                    <div class="divide-y">
                        <?php foreach ($cartItems as $item): ?>
                        <div class="p-6 flex items-center space-x-4">
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>"
                                 class="w-20 h-20 object-cover rounded-lg">
                            
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($item['name']); ?></h3>
                                <p class="text-gray-600 text-sm">
                                    Size: <?php echo htmlspecialchars($item['size']); ?> | 
                                    Color: <?php echo htmlspecialchars($item['color']); ?>
                                </p>
                                <p class="text-primary font-semibold">$<?php echo number_format($item['price'], 2); ?></p>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <!-- Quantity Controls -->
                                <form method="POST" class="flex items-center space-x-2">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="update_quantity">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    
                                    <button type="button" onclick="updateQuantity(<?php echo $item['id']; ?>, -1)" 
                                            class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-50">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    
                                    <input type="number" id="quantity-<?php echo $item['id']; ?>" 
                                           name="quantity" value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="10" 
                                           class="w-16 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                                           onchange="this.form.submit()">
                                    
                                    <button type="button" onclick="updateQuantity(<?php echo $item['id']; ?>, 1)" 
                                            class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-50">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </form>
                                
                                <!-- Remove Button -->
                                <form method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="remove_item">
                                    <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" 
                                            class="text-red-500 hover:text-red-700 transition-colors"
                                            onclick="return confirm('Are you sure you want to remove this item?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Clear Cart Button -->
                <div class="mt-4">
                    <form method="POST" class="inline">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="clear_cart">
                        <button type="submit" 
                                class="text-gray-600 hover:text-red-600 transition-colors"
                                onclick="return confirm('Are you sure you want to clear your cart?')">
                            <i class="fas fa-trash mr-2"></i>Clear Cart
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                    
                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold">$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-semibold"><?php echo $total >= 50 ? 'Free' : '$5.99'; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-semibold">$<?php echo number_format($total * 0.08, 2); ?></span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold">Total</span>
                                <span class="text-lg font-semibold text-primary">
                                    $<?php echo number_format($total + ($total * 0.08) + ($total >= 50 ? 0 : 5.99), 2); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="checkout.php" 
                       class="w-full bg-primary text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center block">
                        Proceed to Checkout
                    </a>
                    
                    <div class="mt-4 text-center">
                        <a href="products.php" class="text-primary hover:text-blue-700 text-sm">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // User Dropdown Functionality
        const userDropdownBtn = document.getElementById('user-dropdown-btn');
        const userDropdownMenu = document.getElementById('user-dropdown-menu');
        
        if (userDropdownBtn && userDropdownMenu) {
            userDropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdownMenu.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userDropdownBtn.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                    userDropdownMenu.classList.add('hidden');
                }
            });
            
            // Close dropdown when pressing Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    userDropdownMenu.classList.add('hidden');
                }
            });
        }

        function updateQuantity(itemId, change) {
            const quantityInput = document.getElementById(`quantity-${itemId}`);
            let currentValue = parseInt(quantityInput.value) || 1;
            let newValue = currentValue + change;
            
            // Ensure value is between 1 and 10
            newValue = Math.max(1, Math.min(10, newValue));
            quantityInput.value = newValue;
            
            // Submit the form
            quantityInput.form.submit();
        }
    </script>
</body>
</html> 
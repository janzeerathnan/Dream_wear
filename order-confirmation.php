<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$orderId = intval($_GET['id'] ?? 0);
if (!$orderId) {
    redirect('orders.php');
}

$order = new Order();
$orderData = $order->findById($orderId);

if (!$orderData || $orderData['user_id'] != $_SESSION['user_id']) {
    redirect('orders.php');
}

$orderItems = $order->getOrderItems($orderId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Dream Wear</title>
    
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
                    <a href="cart.php" class="relative text-gray-700 hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        <div class="text-center mb-8">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                <i class="fas fa-check-circle text-2xl mb-4"></i>
                <h1 class="text-3xl font-bold text-green-800 mb-2">Order Confirmed!</h1>
                <p class="text-lg text-green-700">Thank you for your order. We'll process it right away.</p>
            </div>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Order #<?php echo htmlspecialchars($orderData['order_number']); ?></h2>
                    <p class="text-gray-600">Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($orderData['created_at'])); ?></p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <?php echo ucfirst($orderData['status']); ?>
                    </span>
                </div>
            </div>

            <!-- Order Items -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                <div class="space-y-4">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="flex items-center space-x-4 p-4 border rounded-lg">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             class="w-16 h-16 object-cover rounded-lg">
                        
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['name']); ?></h4>
                            <p class="text-gray-600 text-sm">
                                Size: <?php echo htmlspecialchars($item['size']); ?> | 
                                Color: <?php echo htmlspecialchars($item['color']); ?> |
                                Qty: <?php echo $item['quantity']; ?>
                            </p>
                            <?php if (!empty($item['customization_notes'])): ?>
                            <p class="text-gray-500 text-sm mt-1">
                                <strong>Customization:</strong> <?php echo htmlspecialchars($item['customization_notes']); ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">$<?php echo number_format($item['unit_price'], 2); ?></p>
                            <p class="text-sm text-gray-600">Total: $<?php echo number_format($item['total_price'], 2); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="border-t pt-6 mt-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h3>
                        <div class="text-gray-600">
                            <?php echo nl2br(htmlspecialchars($orderData['shipping_address'])); ?>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">$<?php echo number_format($orderData['subtotal'], 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-medium">$<?php echo number_format($orderData['tax'], 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="font-medium">$<?php echo number_format($orderData['shipping_cost'], 2); ?></span>
                            </div>
                            <div class="border-t pt-2">
                                <div class="flex justify-between font-semibold">
                                    <span>Total:</span>
                                    <span class="text-primary">$<?php echo number_format($orderData['total_amount'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700">
                        <strong>Payment Method:</strong> 
                        <?php echo $orderData['payment_method'] === 'cash_on_delivery' ? 'Cash on Delivery' : 'Card Payment'; ?>
                    </p>
                    <p class="text-gray-700">
                        <strong>Payment Status:</strong> 
                        <span class="font-semibold <?php echo $orderData['payment_status'] === 'paid' ? 'text-green-600' : 'text-yellow-600'; ?>">
                            <?php echo ucfirst($orderData['payment_status']); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">What's Next?</h3>
            <div class="space-y-3 text-blue-800">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-envelope text-blue-600 mt-1"></i>
                    <div>
                        <p class="font-medium">Order Confirmation Email</p>
                        <p class="text-sm">You'll receive a confirmation email shortly with all the details.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <i class="fas fa-truck text-blue-600 mt-1"></i>
                    <div>
                        <p class="font-medium">Shipping Updates</p>
                        <p class="text-sm">We'll notify you when your order ships and provide tracking information.</p>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <i class="fas fa-clock text-blue-600 mt-1"></i>
                    <div>
                        <p class="font-medium">Processing Time</p>
                        <p class="text-sm">Orders are typically processed within 1-2 business days.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="orders.php" class="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center">
                View All Orders
            </a>
            <a href="products.php" class="bg-gray-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-700 transition-colors text-center">
                Continue Shopping
            </a>
            <a href="index.php" class="border border-primary text-primary px-6 py-3 rounded-lg font-semibold hover:bg-primary hover:text-white transition-colors text-center">
                Back to Home
            </a>
        </div>
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
    </script>
</body>
</html> 
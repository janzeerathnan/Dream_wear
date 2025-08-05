<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = new User();
$cart = new Cart();
$userData = $user->findById($_SESSION['user_id']);
$cartItems = $cart->getItems($_SESSION['user_id']);

// Check if user has complete address
if (!hasCompleteAddress($userData)) {
    $_SESSION['error'] = 'Please complete your shipping address in your profile before placing an order.';
    redirect('profile.php');
}

// Calculate totals
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.08; // 8% tax
$shipping = $subtotal >= 50 ? 0 : 5.99;
$total = $subtotal + $tax + $shipping;

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = sanitizeInput($_POST['payment_method'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!validateCSRFToken($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } elseif (!validatePaymentMethod($payment_method)) {
        $error = 'Please select a valid payment method.';
    } else {
        // Validate card details if card payment is selected
        if ($payment_method === 'card_payment') {
            $card_number = sanitizeInput($_POST['card_number'] ?? '');
            $expiry_month = sanitizeInput($_POST['expiry_month'] ?? '');
            $expiry_year = sanitizeInput($_POST['expiry_year'] ?? '');
            $cvv = sanitizeInput($_POST['cvv'] ?? '');
            $card_holder = sanitizeInput($_POST['card_holder'] ?? '');
            
            if (!validateCardDetails($card_number, $expiry_month, $expiry_year, $cvv)) {
                $error = 'Please enter valid card details.';
            } elseif (empty($card_holder)) {
                $error = 'Please enter the card holder name.';
            }
        }
        
        if (empty($error)) {
            // Create order
            $order = new Order();
            
            $shippingAddress = $userData['address'] . "\n" . 
                             $userData['city'] . ", " . $userData['state'] . " " . $userData['zip_code'] . "\n" . 
                             $userData['country'];
            
            $orderData = [
                'user_id' => $_SESSION['user_id'],
                'order_number' => generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping_cost' => $shipping,
                'total_amount' => $total,
                'shipping_address' => $shippingAddress,
                'billing_address' => $shippingAddress,
                'payment_method' => $payment_method,
                'payment_status' => $payment_method === 'cash_on_delivery' ? 'pending' : 'paid',
                'notes' => 'Payment method: ' . ($payment_method === 'cash_on_delivery' ? 'Cash on Delivery' : 'Card Payment')
            ];
            
            try {
                $orderId = $order->create($orderData);
                
                if ($orderId) {
                    // Add order items
                    foreach ($cartItems as $item) {
                        $orderItemData = [
                            'order_id' => $orderId,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['price'],
                            'total_price' => $item['price'] * $item['quantity'],
                            'size' => $item['size'],
                            'color' => $item['color'],
                            'customization_notes' => $item['customization_notes'] ?? ''
                        ];
                        
                        $order->addOrderItem($orderItemData);
                    }
                    
                    // Clear cart
                    $cart->clearCart($_SESSION['user_id']);
                    
                    // Log activity
                    logActivity($_SESSION['user_id'], 'order_placed', "Order #{$orderData['order_number']} placed with {$payment_method}");
                    
                    $success = 'Order placed successfully! Your order number is: ' . $orderData['order_number'];
                    
                    // Redirect to order confirmation
                    $_SESSION['order_success'] = $orderData['order_number'];
                    redirect('order-confirmation.php?id=' . $orderId);
                } else {
                    $error = 'Failed to create order. Please try again.';
                }
            } catch (Exception $e) {
                $error = 'An error occurred while placing your order. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Dream Wear</title>
    
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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout</h1>
            <p class="text-gray-600">Complete your order</p>
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
                
                <div class="space-y-4 mb-6">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="flex items-center space-x-4">
                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             class="w-16 h-16 object-cover rounded-lg">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-gray-600 text-sm">
                                Size: <?php echo htmlspecialchars($item['size']); ?> | 
                                Color: <?php echo htmlspecialchars($item['color']); ?> |
                                Qty: <?php echo $item['quantity']; ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold">$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax (8%)</span>
                        <span class="font-semibold">$<?php echo number_format($tax, 2); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-semibold"><?php echo $shipping > 0 ? '$' . number_format($shipping, 2) : 'Free'; ?></span>
                    </div>
                    <div class="border-t pt-2">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-lg font-semibold text-primary">$<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Method</h2>
                
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <!-- Payment Method Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Select Payment Method</label>
                        <div class="space-y-3">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" name="payment_method" value="cash_on_delivery" class="text-primary focus:ring-primary" checked>
                                <span class="text-gray-900">Cash on Delivery</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="radio" name="payment_method" value="card_payment" class="text-primary focus:ring-primary">
                                <span class="text-gray-900">Card Payment</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Card Payment Details (initially hidden) -->
                    <div id="card-payment-details" class="hidden space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Card Holder Name</label>
                            <input type="text" name="card_holder" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                            <input type="text" name="card_number" placeholder="1234 5678 9012 3456" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Month</label>
                                <select name="expiry_month" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                    <option value="">MM</option>
                                    <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Year</label>
                                <select name="expiry_year" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                    <option value="">YYYY</option>
                                    <?php for ($i = date('Y'); $i <= date('Y') + 10; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                <input type="text" name="cvv" placeholder="123" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Address -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Shipping Address</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($shippingAddress)); ?></p>
                            <a href="profile.php" class="text-primary hover:text-blue-700 text-sm">Update Address</a>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full bg-primary text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Place Order
                    </button>
                </form>
            </div>
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

        // Payment Method Toggle
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const cardDetails = document.getElementById('card-payment-details');
        
        paymentMethods.forEach(method => {
            method.addEventListener('change', () => {
                if (method.value === 'card_payment') {
                    cardDetails.classList.remove('hidden');
                } else {
                    cardDetails.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html> 
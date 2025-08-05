<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$order = new Order();
$orders = $order->findByUser($_SESSION['user_id']);

// Get order details if requested
$orderId = intval($_GET['id'] ?? 0);
$orderDetails = null;
$orderItems = [];

if ($orderId) {
    $orderDetails = $order->findById($orderId);
    if ($orderDetails && $orderDetails['user_id'] == $_SESSION['user_id']) {
        $orderItems = $order->getOrderItems($orderId);
    } else {
        redirect('orders.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Dream Wear</title>
    
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
                    
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-primary transition-colors">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="orders.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 bg-gray-50">My Orders</a>
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
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Orders</h1>
            <p class="text-gray-600">Track your order history and status</p>
        </div>

        <?php if ($orderDetails): ?>
        <!-- Order Details -->
        <div class="mb-6">
            <a href="orders.php" class="text-primary hover:text-blue-700 mb-4 inline-block">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Order #<?php echo htmlspecialchars($orderDetails['order_number']); ?></h2>
                    <p class="text-gray-600">Placed on <?php echo date('F j, Y \a\t g:i A', strtotime($orderDetails['created_at'])); ?></p>
                </div>
                <div class="text-right">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        <?php 
                        switch($orderDetails['status']) {
                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                            case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                            case 'shipped': echo 'bg-purple-100 text-purple-800'; break;
                            case 'delivered': echo 'bg-green-100 text-green-800'; break;
                            case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                            default: echo 'bg-gray-100 text-gray-800';
                        }
                        ?>">
                        <?php echo ucfirst($orderDetails['status']); ?>
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
                            <?php echo nl2br(htmlspecialchars($orderDetails['shipping_address'])); ?>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">$<?php echo number_format($orderDetails['subtotal'], 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-medium">$<?php echo number_format($orderDetails['tax'], 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="font-medium">$<?php echo number_format($orderDetails['shipping_cost'], 2); ?></span>
                            </div>
                            <div class="border-t pt-2">
                                <div class="flex justify-between font-semibold">
                                    <span>Total:</span>
                                    <span class="text-primary">$<?php echo number_format($orderDetails['total_amount'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($orderDetails['notes'])): ?>
            <div class="border-t pt-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Order Notes</h3>
                <p class="text-gray-600"><?php echo htmlspecialchars($orderDetails['notes']); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <?php else: ?>
        <!-- Orders List -->
        <?php if (empty($orders)): ?>
        <div class="text-center py-12">
            <i class="fas fa-shopping-bag text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No orders yet</h3>
            <p class="text-gray-500 mb-6">You haven't placed any orders yet. Start shopping to see your orders here.</p>
            <a href="products.php" class="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                Start Shopping
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-6">
            <?php foreach ($orders as $order): ?>
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Order #<?php echo htmlspecialchars($order['order_number']); ?>
                        </h3>
                        <p class="text-gray-600 text-sm">
                            Placed on <?php echo date('F j, Y', strtotime($order['created_at'])); ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            <?php 
                            switch($order['status']) {
                                case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                                case 'shipped': echo 'bg-purple-100 text-purple-800'; break;
                                case 'delivered': echo 'bg-green-100 text-green-800'; break;
                                case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                default: echo 'bg-gray-100 text-gray-800';
                            }
                            ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-600">
                        <p>Total: <span class="font-semibold text-gray-900">$<?php echo number_format($order['total_amount'], 2); ?></span></p>
                        <p>Items: <?php echo count($order->getOrderItems($order['id'])); ?> product(s)</p>
                    </div>
                    
                    <a href="orders.php?id=<?php echo $order['id']; ?>" 
                       class="bg-primary text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                        View Details
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html> 
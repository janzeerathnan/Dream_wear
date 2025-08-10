<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$user = new User();
$product = new Product();
$order = new Order();

// Get new orders count for notification
$newOrders = [];
$allOrders = $order->getAll();
foreach ($allOrders as $ord) {
    $orderTime = strtotime($ord['created_at']);
    if ($orderTime > (time() - 86400)) { // Last 24 hours
        $newOrders[] = $ord;
    }
}
$newOrdersCount = count($newOrders);

// Get analytics data
$totalUsers = count($user->getAll());
$totalProducts = count($product->getAll());
$totalOrders = count($order->getAll());

// Get recent orders for revenue calculation
$recentOrders = $order->getAll();
$totalRevenue = 0;
$monthlyRevenue = [];
$categorySales = [];

foreach ($recentOrders as $ord) {
    $totalRevenue += $ord['total_amount'];
    
    // Monthly revenue
    $month = date('Y-m', strtotime($ord['created_at']));
    if (!isset($monthlyRevenue[$month])) {
        $monthlyRevenue[$month] = 0;
    }
    $monthlyRevenue[$month] += $ord['total_amount'];
    
    // Get order items for category analysis
    $orderItems = $order->getOrderItems($ord['id']);
    foreach ($orderItems as $item) {
        $productData = $product->findById($item['product_id']);
        if ($productData) {
            $category = $productData['category'];
            if (!isset($categorySales[$category])) {
                $categorySales[$category] = 0;
            }
            $categorySales[$category] += $item['total_price'];
        }
    }
}

// Get top selling products
$topProducts = [];
foreach ($recentOrders as $ord) {
    $orderItems = $order->getOrderItems($ord['id']);
    foreach ($orderItems as $item) {
        $productData = $product->findById($item['product_id']);
        if ($productData) {
            $productName = $productData['name'];
            if (!isset($topProducts[$productName])) {
                $topProducts[$productName] = 0;
            }
            $topProducts[$productName] += $item['quantity'];
        }
    }
}

// Sort top products by quantity
arsort($topProducts);
$topProducts = array_slice($topProducts, 0, 5, true);

// Get user registration trends
$users = $user->getAll();
$userRegistrationTrends = [];
foreach ($users as $usr) {
    $month = date('Y-m', strtotime($usr['created_at']));
    if (!isset($userRegistrationTrends[$month])) {
        $userRegistrationTrends[$month] = 0;
    }
    $userRegistrationTrends[$month]++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin Panel</title>
    
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
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
    <!-- Admin Header -->
    <header class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    
                        <img src="../logo.jpg" alt="Admin" class="h-8 w-auto">
                       
                    
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="../logout.php" class="text-gray-700 hover:text-primary transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="dashboard.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="products.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-tshirt mr-3"></i>
                        Products
                    </a>
                    <a href="orders.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg relative">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Orders
                        <?php if ($newOrdersCount > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                            <?php echo $newOrdersCount; ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <a href="users.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                    <a href="analytics.php" class="flex items-center px-4 py-2 text-primary bg-blue-50 rounded-lg">
                        <i class="fas fa-chart-bar mr-3"></i>
                        Analytics
                    </a>
                    <a href="settings.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-cog mr-3"></i>
                        Settings
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Analytics</h1>
                <p class="text-gray-600">Comprehensive insights into your business performance</p>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $totalUsers; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-tshirt text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $totalProducts; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="fas fa-shopping-cart text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $totalOrders; ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="fas fa-dollar-sign text-red-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">$<?php echo number_format($totalRevenue, 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Monthly Revenue</h3>
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>

                <!-- Category Sales Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales by Category</h3>
                    <canvas id="categoryChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Top Products and User Registration -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Top Selling Products -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Selling Products</h3>
                    <div class="space-y-4">
                        <?php foreach ($topProducts as $productName => $quantity): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                    <?php echo array_search($productName, array_keys($topProducts)) + 1; ?>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($productName); ?></p>
                                    <p class="text-sm text-gray-500"><?php echo $quantity; ?> units sold</p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- User Registration Trends -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">User Registration Trends</h3>
                    <canvas id="userChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Detailed Statistics -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="text-md font-semibold text-gray-700 mb-2">Order Statistics</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Average Order Value:</span>
                                <span class="font-medium">$<?php echo $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2) : '0.00'; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Revenue:</span>
                                <span class="font-medium">$<?php echo number_format($totalRevenue, 2); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Orders This Month:</span>
                                <span class="font-medium"><?php echo count(array_filter($recentOrders, function($order) {
                                    return date('Y-m', strtotime($order['created_at'])) === date('Y-m');
                                })); ?></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-semibold text-gray-700 mb-2">User Statistics</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Users:</span>
                                <span class="font-medium"><?php echo $totalUsers; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">New Users This Month:</span>
                                <span class="font-medium"><?php echo count(array_filter($users, function($user) {
                                    return date('Y-m', strtotime($user['created_at'])) === date('Y-m');
                                })); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Active Users:</span>
                                <span class="font-medium"><?php echo count(array_filter($users, function($user) {
                                    return $user['is_active'] == 1;
                                })); ?></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-semibold text-gray-700 mb-2">Product Statistics</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Products:</span>
                                <span class="font-medium"><?php echo $totalProducts; ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Featured Products:</span>
                                <span class="font-medium"><?php echo count(array_filter($product->getAll(), function($prod) {
                                    return $prod['is_featured'] == 1;
                                })); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Low Stock Items:</span>
                                <span class="font-medium"><?php echo count(array_filter($product->getAll(), function($prod) {
                                    return $prod['stock_quantity'] <= 5;
                                })); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($monthlyRevenue)); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode(array_values($monthlyRevenue)); ?>,
                    borderColor: '#1e40af',
                    backgroundColor: 'rgba(30, 64, 175, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Category Sales Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_keys($categorySales)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($categorySales)); ?>,
                    backgroundColor: [
                        '#1e40af',
                        '#dc2626',
                        '#f59e0b',
                        '#10b981',
                        '#8b5cf6'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // User Registration Chart
        const userCtx = document.getElementById('userChart').getContext('2d');
        new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($userRegistrationTrends)); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode(array_values($userRegistrationTrends)); ?>,
                    backgroundColor: '#10b981',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html> 
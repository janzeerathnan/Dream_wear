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

// Get dashboard statistics
$totalUsers = count($user->getAll());
$totalProducts = count($product->getAll());
$totalOrders = count($order->getAll());

// Get recent orders
$recentOrders = $order->getAll(5);

// Get low stock products
$lowStockProducts = $product->getAll(['stock_quantity' => 5], 5);

// Get user activity (simplified)
$recentUsers = $user->getAll(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dream Wear</title>
    
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
    <!-- Fixed Admin Header -->
    <header class="bg-white shadow-lg fixed top-0 left-0 right-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    
                        <img src="../logo.jpg" alt="Dream Wear" class="h-8 w-auto">
                        <span class="text-xl font-bold text-gray-900">Dream Wear</span>
                        
                    
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

    <!-- Spacer for fixed header -->
    <div class="h-16"></div>

    <div class="flex">
        <!-- Fixed Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen fixed left-0 top-16 z-40">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="dashboard.php" class="flex items-center px-4 py-2 text-primary bg-blue-50 rounded-lg">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="products.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-tshirt mr-3"></i>
                        Products
                    </a>
                    <a href="orders.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Orders
                    </a>
                    <a href="users.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                    <a href="analytics.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
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

        <!-- Main Content with left margin for fixed sidebar -->
        <main class="flex-1 p-8 ml-64">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600">Welcome to your Dream Wear admin dashboard</p>
            </div>

            <!-- Statistics Cards -->
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
                            <p class="text-sm font-medium text-gray-600">Revenue</p>
                            <p class="text-2xl font-bold text-gray-900">$12,450</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Sales Chart -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Overview</h3>
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>

                <!-- Product Categories -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Categories</h3>
                    <canvas id="categoryChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                        <a href="orders.php" class="text-primary hover:text-blue-700 text-sm">View All</a>
                    </div>
                    
                    <?php if (empty($recentOrders)): ?>
                    <p class="text-gray-500 text-center py-4">No recent orders</p>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recentOrders as $order): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">Order #<?php echo htmlspecialchars($order['order_number']); ?></p>
                                <p class="text-sm text-gray-600">$<?php echo number_format($order['total_amount'], 2); ?></p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php echo $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                        ($order['status'] === 'shipped' ? 'bg-blue-100 text-blue-800' : 
                                        'bg-green-100 text-green-800'); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                                <p class="text-xs text-gray-500 mt-1"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Low Stock Alert -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Low Stock Alert</h3>
                        <a href="products.php" class="text-primary hover:text-blue-700 text-sm">View All</a>
                    </div>
                    
                    <?php if (empty($lowStockProducts)): ?>
                    <p class="text-gray-500 text-center py-4">All products are well stocked</p>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($lowStockProducts as $product): ?>
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars($product['name']); ?></p>
                                <p class="text-sm text-gray-600">Stock: <?php echo $product['stock_quantity']; ?></p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Low Stock
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </main>
    </div>

    <script>
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Sales',
                    data: [12000, 19000, 15000, 25000, 22000, 30000],
                    borderColor: '#1e40af',
                    backgroundColor: 'rgba(30, 64, 175, 0.1)',
                    tension: 0.4
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
                        beginAtZero: true
                    }
                }
            }
        });

        // Category Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: ['Football', 'Basketball', 'Baseball', 'Hockey', 'Soccer'],
                datasets: [{
                    data: [30, 25, 20, 15, 10],
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
    </script>
</body>
</html> 
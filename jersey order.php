<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$order = new Order();
$user = new User();
$product = new Product();
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } else {
        switch ($action) {
            case 'update_status':
                $order_id = intval($_POST['order_id'] ?? 0);
                $status = sanitizeInput($_POST['status'] ?? '');
                
                if ($order->update($order_id, ['status' => $status])) {
                    $success = 'Order status updated successfully!';
                } else {
                    $error = 'Failed to update order status.';
                }
                break;
        }
    }
}

// Get all orders with user information
$orders = $order->getAll();
$orderDetails = [];

// Get order details for each order
foreach ($orders as $ord) {
    $ord['user'] = $user->findById($ord['user_id']);
    $ord['items'] = $order->getOrderItems($ord['id']);
    $orderDetails[] = $ord;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Dream Wear Admin</title>
    
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
<body class="bg-gray-100">
    <!-- Admin Header -->
    <header class="bg-white shadow-lg">
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
                    <a href="orders.php" class="flex items-center px-4 py-2 text-primary bg-blue-50 rounded-lg">
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

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Orders Management</h1>
                <p class="text-gray-600">Manage and track customer orders</p>
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

            <!-- Orders Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">All Orders</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($orderDetails as $ord): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">#<?php echo htmlspecialchars($ord['order_number']); ?></div>
                                    <div class="text-sm text-gray-500">ID: <?php echo $ord['id']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($ord['user']['first_name'] . ' ' . $ord['user']['last_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($ord['user']['email']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo count($ord['items']); ?> items</div>
                                    <div class="text-sm text-gray-500">
                                        <?php 
                                        $itemNames = array_map(function($item) {
                                            return $item['name'];
                                        }, array_slice($ord['items'], 0, 2));
                                        echo htmlspecialchars(implode(', ', $itemNames));
                                        if (count($ord['items']) > 2) echo '...';
                                        ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">$<?php echo number_format($ord['total_amount'], 2); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo $ord['payment_status']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        <?php 
                                        switch($ord['status']) {
                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'shipped': echo 'bg-purple-100 text-purple-800'; break;
                                            case 'delivered': echo 'bg-green-100 text-green-800'; break;
                                            case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?php echo ucfirst($ord['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo date('M j, Y', strtotime($ord['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openOrderModal(<?php echo htmlspecialchars(json_encode($ord)); ?>)" class="text-primary hover:text-blue-700 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="openStatusModal(<?php echo $ord['id']; ?>, '<?php echo $ord['status']; ?>')" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-4xl max-h-screen overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Order Details</h3>
                    <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="orderDetails" class="space-y-6">
                    <!-- Order details will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Update Order Status</h3>
                    <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" id="status_order_id">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                        <select name="status" id="status_select" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeStatusModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-700">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openOrderModal(order) {
            const detailsDiv = document.getElementById('orderDetails');
            
            let itemsHtml = '';
            order.items.forEach(item => {
                itemsHtml += `
                    <div class="flex items-center space-x-4 p-4 border rounded-lg">
                        <img src="${item.image_url}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">${item.name}</h4>
                            <p class="text-gray-600 text-sm">
                                Size: ${item.size} | Color: ${item.color} | Qty: ${item.quantity}
                            </p>
                            ${item.customization_notes ? `<p class="text-gray-500 text-sm mt-1"><strong>Customization:</strong> ${item.customization_notes}</p>` : ''}
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">$${parseFloat(item.unit_price).toFixed(2)}</p>
                            <p class="text-sm text-gray-600">Total: $${parseFloat(item.total_price).toFixed(2)}</p>
                        </div>
                    </div>
                `;
            });
            
            detailsDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Number:</span>
                                <span class="font-medium">#${order.order_number}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Date:</span>
                                <span class="font-medium">${new Date(order.created_at).toLocaleDateString()}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-medium">${order.status}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Status:</span>
                                <span class="font-medium">${order.payment_status}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Name:</span>
                                <span class="font-medium">${order.user.first_name} ${order.user.last_name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Email:</span>
                                <span class="font-medium">${order.user.email}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Phone:</span>
                                <span class="font-medium">${order.user.phone || 'N/A'}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="border-t pt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h4>
                    <p class="text-gray-600 text-sm">${order.shipping_address}</p>
                </div>
                
                <div class="border-t pt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h4>
                    <div class="space-y-4">
                        ${itemsHtml}
                    </div>
                </div>
                
                <div class="border-t pt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal:</span>
                            <span class="font-medium">$${parseFloat(order.subtotal).toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax:</span>
                            <span class="font-medium">$${parseFloat(order.tax).toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping:</span>
                            <span class="font-medium">$${parseFloat(order.shipping_cost).toFixed(2)}</span>
                        </div>
                        <div class="border-t pt-2">
                            <div class="flex justify-between font-semibold">
                                <span>Total:</span>
                                <span class="text-primary">$${parseFloat(order.total_amount).toFixed(2)}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                ${order.notes ? `
                <div class="border-t pt-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">Order Notes</h4>
                    <p class="text-gray-600 text-sm">${order.notes}</p>
                </div>
                ` : ''}
            `;
            
            document.getElementById('orderModal').classList.remove('hidden');
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.add('hidden');
        }

        function openStatusModal(orderId, currentStatus) {
            document.getElementById('status_order_id').value = orderId;
            document.getElementById('status_select').value = currentStatus;
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }
    </script>
</body>
</html> 
<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$product = new Product();
$error = '';
$success = '';

// Get new orders count for notification
$order = new Order();
$newOrders = [];
$allOrders = $order->getAll();
foreach ($allOrders as $ord) {
    $orderTime = strtotime($ord['created_at']);
    if ($orderTime > (time() - 86400)) { // Last 24 hours
        $newOrders[] = $ord;
    }
}
$newOrdersCount = count($newOrders);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } else {
        switch ($action) {
            case 'create':
                $name = sanitizeInput($_POST['name'] ?? '');
                $description = sanitizeInput($_POST['description'] ?? '');
                $price = floatval($_POST['price'] ?? 0);
                $category = sanitizeInput($_POST['category'] ?? '');
                $sport_type = sanitizeInput($_POST['sport_type'] ?? '');
                $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
                
                // Handle checkbox arrays for colors and sizes
                $colors = isset($_POST['colors']) && is_array($_POST['colors']) ? json_encode($_POST['colors']) : json_encode([]);
                $sizes = isset($_POST['sizes']) && is_array($_POST['sizes']) ? json_encode($_POST['sizes']) : json_encode([]);
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;

                if (empty($name) || empty($price) || empty($category) || empty($sport_type)) {
                    $error = 'Name, price, category, and sport type are required.';
                } else {
                    // Handle image upload
                    $image_url = 'assets/images/products/default.jpg';
                    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = '../assets/images/products/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_info = pathinfo($_FILES['product_image']['name']);
                        $file_extension = strtolower($file_info['extension']);
                        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        
                        if (in_array($file_extension, $allowed_extensions)) {
                            $file_name = uniqid() . '_' . time() . '.' . $file_extension;
                            $upload_path = $upload_dir . $file_name;
                            
                            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                                $image_url = 'assets/images/products/' . $file_name;
                            } else {
                                $error = 'Failed to upload image. Please try again.';
                            }
                        } else {
                            $error = 'Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.';
                        }
                    }

                    if (empty($error)) {
                        $productData = [
                            'name' => $name,
                            'description' => $description,
                            'price' => $price,
                            'category' => $category,
                            'sport_type' => $sport_type,
                            'stock_quantity' => $stock_quantity,
                            'colors' => $colors,
                            'sizes' => $sizes,
                            'is_featured' => $is_featured,
                            'image_url' => $image_url
                        ];

                        if ($product->create($productData)) {
                            $success = 'Product created successfully!';
                        } else {
                            $error = 'Failed to create product.';
                        }
                    }
                }
                break;

            case 'update':
                $id = intval($_POST['id'] ?? 0);
                $name = sanitizeInput($_POST['name'] ?? '');
                $description = sanitizeInput($_POST['description'] ?? '');
                $price = floatval($_POST['price'] ?? 0);
                $category = sanitizeInput($_POST['category'] ?? '');
                $sport_type = sanitizeInput($_POST['sport_type'] ?? '');
                $stock_quantity = intval($_POST['stock_quantity'] ?? 0);
                
                // Handle checkbox arrays for colors and sizes
                $colors = isset($_POST['colors']) && is_array($_POST['colors']) ? json_encode($_POST['colors']) : json_encode([]);
                $sizes = isset($_POST['sizes']) && is_array($_POST['sizes']) ? json_encode($_POST['sizes']) : json_encode([]);
                $is_featured = isset($_POST['is_featured']) ? 1 : 0;

                if (empty($name) || empty($price) || empty($category) || empty($sport_type)) {
                    $error = 'Name, price, category, and sport type are required.';
                } else {
                    // Handle image upload for update
                    $current_product = $product->findById($id);
                    $image_url = $current_product['image_url'] ?? 'assets/images/products/default.jpg';
                    
                    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
                        $upload_dir = '../assets/images/products/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_info = pathinfo($_FILES['product_image']['name']);
                        $file_extension = strtolower($file_info['extension']);
                        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        
                        if (in_array($file_extension, $allowed_extensions)) {
                            $file_name = uniqid() . '_' . time() . '.' . $file_extension;
                            $upload_path = $upload_dir . $file_name;
                            
                            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                                // Delete old image if it's not the default
                                if ($image_url !== 'assets/images/products/default.jpg' && file_exists('../' . $image_url)) {
                                    unlink('../' . $image_url);
                                }
                                $image_url = 'assets/images/products/' . $file_name;
                            } else {
                                $error = 'Failed to upload image. Please try again.';
                            }
                        } else {
                            $error = 'Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.';
                        }
                    }

                    if (empty($error)) {
                        $productData = [
                            'name' => $name,
                            'description' => $description,
                            'price' => $price,
                            'category' => $category,
                            'sport_type' => $sport_type,
                            'stock_quantity' => $stock_quantity,
                            'colors' => $colors,
                            'sizes' => $sizes,
                            'is_featured' => $is_featured,
                            'image_url' => $image_url
                        ];

                        if ($product->update($id, $productData)) {
                            $success = 'Product updated successfully!';
                        } else {
                            $error = 'Failed to update product.';
                        }
                    }
                }
                break;

            case 'delete':
                $id = intval($_POST['id'] ?? 0);
                if ($product->delete($id)) {
                    $success = 'Product deleted successfully!';
                } else {
                    $error = 'Failed to delete product.';
                }
                break;
        }
    }
}

// Handle search and filter parameters
$search = $_GET['search'] ?? '';
$categoryFilter = $_GET['category_filter'] ?? '';
$statusFilter = $_GET['status_filter'] ?? '';

// Get all products for admin (including inactive ones)
try {
    $products = $product->getAllForAdmin();
    
    // Apply search filter
    if (!empty($search)) {
        $products = array_filter($products, function($prod) use ($search) {
            $searchLower = strtolower($search);
            return strpos(strtolower($prod['name'] ?? ''), $searchLower) !== false ||
                   strpos(strtolower($prod['category'] ?? ''), $searchLower) !== false ||
                   strpos(strtolower($prod['sport_type'] ?? ''), $searchLower) !== false;
        });
    }
    
    // Apply category filter
    if (!empty($categoryFilter)) {
        $products = array_filter($products, function($prod) use ($categoryFilter) {
            return ($prod['category'] ?? '') === $categoryFilter;
        });
    }
    
    // Apply status filter
    if (!empty($statusFilter)) {
        $products = array_filter($products, function($prod) use ($statusFilter) {
            if ($statusFilter === 'active') {
                return ($prod['is_active'] ?? 1) == 1;
            } else {
                return ($prod['is_active'] ?? 1) == 0;
            }
        });
    }
    
    // Re-index array after filtering
    $products = array_values($products);
    
    if (empty($products)) {
        $error = 'No products found matching your search criteria.';
    }
} catch (Exception $e) {
    $error = 'Error retrieving products: ' . $e->getMessage();
    $products = [];
    error_log("Product retrieval error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - Admin Panel</title>
    
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
    <!-- Fixed Admin Header -->
    <header class="bg-white shadow-lg fixed top-0 left-0 right-0 z-50">
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

    <!-- Spacer for fixed header -->
    <div class="h-16"></div>

    <div class="flex">
        <!-- Fixed Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen fixed left-0 top-16 z-40">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="dashboard.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="products.php" class="flex items-center px-4 py-2 text-primary bg-blue-50 rounded-lg">
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
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Products Management</h1>
                        <p class="text-gray-600">Manage your product catalog</p>
                    </div>
                    <button onclick="openCreateModal()" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Product
                    </button>
                </div>
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

            <?php if (isset($_GET['debug']) && $_GET['debug'] === '1'): ?>
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">
                    <strong>Debug Info:</strong><br>
                    Total Products: <?php echo count($products); ?><br>
                    <?php if (!empty($products)): ?>
                    First Product: <?php echo htmlspecialchars(json_encode(array_slice($products[0], 0, 5))); ?>
                    <?php endif; ?>
                </span>
            </div>
            <?php endif; ?>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="flex-1">
                        <form method="GET" class="flex gap-2">
                            <input type="text" name="search" placeholder="Search products by name, category, or sport type..." 
                                   value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>"
                                   class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <button type="submit" class="bg-primary text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-search mr-2"></i>Search
                            </button>
                            <?php if (!empty($_GET['search'])): ?>
                            <a href="products.php" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600 transition-colors">
                                <i class="fas fa-times mr-2"></i>Clear
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                    <div class="flex gap-2">
                        <select name="category_filter" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <option value="">All Categories</option>
                            <option value="Cricket" <?php echo ($_GET['category_filter'] ?? '') === 'Cricket' ? 'selected' : ''; ?>>Cricket</option>
                            <option value="Football" <?php echo ($_GET['category_filter'] ?? '') === 'Football' ? 'selected' : ''; ?>>Football</option>
                            <option value="Rugby" <?php echo ($_GET['category_filter'] ?? '') === 'Rugby' ? 'selected' : ''; ?>>Rugby</option>
                            <option value="Tennis" <?php echo ($_GET['category_filter'] ?? '') === 'Tennis' ? 'selected' : ''; ?>>Tennis</option>
                            <option value="Badminton" <?php echo ($_GET['category_filter'] ?? '') === 'Badminton' ? 'selected' : ''; ?>>Badminton</option>
                            <option value="Volleyball" <?php echo ($_GET['category_filter'] ?? '') === 'Volleyball' ? 'selected' : ''; ?>>Volleyball</option>
                            <option value="Basketball" <?php echo ($_GET['category_filter'] ?? '') === 'Basketball' ? 'selected' : ''; ?>>Basketball</option>
                            <option value="Hockey" <?php echo ($_GET['category_filter'] ?? '') === 'Hockey' ? 'selected' : ''; ?>>Hockey</option>
                            <option value="Swimming" <?php echo ($_GET['category_filter'] ?? '') === 'Swimming' ? 'selected' : ''; ?>>Swimming</option>
                            <option value="Athletics" <?php echo ($_GET['category_filter'] ?? '') === 'Athletics' ? 'selected' : ''; ?>>Athletics</option>
                        </select>
                        <select name="status_filter" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <option value="">All Status</option>
                            <option value="active" <?php echo ($_GET['status_filter'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($_GET['status_filter'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Products Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">All Products (<?php echo count($products); ?> found)</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No products found. Please add some products to get started.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($products as $prod): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-lg object-cover" src="../<?php echo htmlspecialchars($prod['image_url'] ?? 'assets/images/products/default.jpg'); ?>" alt="<?php echo htmlspecialchars($prod['name'] ?? 'Product'); ?>" onerror="this.src='../assets/images/products/default.jpg'">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($prod['name'] ?? 'Unnamed Product'); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($prod['sport_type'] ?? 'N/A'); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?php echo htmlspecialchars($prod['category'] ?? 'N/A'); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($prod['sport_type'] ?? 'N/A'); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Rs. <?php echo number_format($prod['price'] ?? 0, 2); ?></div>
                                    <?php if (!empty($prod['sale_price'])): ?>
                                    <div class="text-sm text-red-600">Sale: Rs. <?php echo number_format($prod['sale_price'], 2); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo ($prod['stock_quantity'] ?? 0) > 10 ? 'bg-green-100 text-green-800' : (($prod['stock_quantity'] ?? 0) > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                        <?php echo $prod['stock_quantity'] ?? 0; ?> in stock
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo ($prod['is_active'] ?? 1) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo ($prod['is_active'] ?? 1) ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($prod)); ?>)" class="text-primary hover:text-blue-700 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteProduct(<?php echo $prod['id'] ?? 0; ?>)" class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Create Product Modal -->
    <div id="createModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Add New Product</h3>
                    <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="create">
                    
                    <!-- Basic Information Section -->
                    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Basic Information</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                                <input type="text" name="name" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200"
                                       placeholder="Enter product name">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                <select name="category" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200">
                                    <option value="">Select Category</option>
                                    <option value="Cricket">Cricket</option>
                                    <option value="Football">Football</option>
                                    <option value="Rugby">Rugby</option>
                                    <option value="Tennis">Tennis</option>
                                    <option value="Badminton">Badminton</option>
                                    <option value="Volleyball">Volleyball</option>
                                    <option value="Basketball">Basketball</option>
                                    <option value="Hockey">Hockey</option>
                                    <option value="Swimming">Swimming</option>
                                    <option value="Athletics">Athletics</option>
                                </select>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sport Type *</label>
                                <select name="sport_type" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200">
                                    <option value="">Select Sport Type</option>
                                    <option value="Sri Lanka Cricket">Sri Lanka Cricket</option>
                                    <option value="Sri Lanka Football">Sri Lanka Football</option>
                                    <option value="Sri Lanka Rugby">Sri Lanka Rugby</option>
                                    <option value="Sri Lanka Tennis">Sri Lanka Tennis</option>
                                    <option value="Sri Lanka Badminton">Sri Lanka Badminton</option>
                                    <option value="Sri Lanka Volleyball">Sri Lanka Volleyball</option>
                                    <option value="Sri Lanka Basketball">Sri Lanka Basketball</option>
                                    <option value="Sri Lanka Hockey">Sri Lanka Hockey</option>
                                    <option value="Sri Lanka Swimming">Sri Lanka Swimming</option>
                                    <option value="Sri Lanka Athletics">Sri Lanka Athletics</option>
                                </select>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price (Rs.) *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rs.</span>
                                    <input type="number" name="price" step="0.01" required 
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200"
                                           placeholder="0.00">
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                                <input type="number" name="stock_quantity" value="0" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200"
                                       placeholder="Enter stock quantity">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Colors and Sizes Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Colors Selection -->
                        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Colors *</h4>
                            <div class="grid grid-cols-3 gap-3 p-4 border border-gray-200 rounded-md bg-gray-50">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Navy Blue" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Navy Blue</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Red" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Red</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="White" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">White</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Black" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Black</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Gold" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Gold</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Blue" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Blue</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Green" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Green</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Orange" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Orange</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Gray" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Gray</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Silver" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Silver</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Pink" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Pink</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Brown" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Brown</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Maroon" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Maroon</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Teal" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Teal</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Cyan" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Cyan</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Indigo" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Indigo</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Violet" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Violet</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Sizes Selection -->
                        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Sizes *</h4>
                            <div class="grid grid-cols-2 gap-3 p-4 border border-gray-200 rounded-md bg-gray-50">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="XS" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">XS</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="S" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">S</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="M" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">M</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="L" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">L</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="XL" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">XL</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="XXL" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">XXL</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="XXXL" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">XXXL</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="Youth S" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Youth S</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="Youth M" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Youth M</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="Youth L" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Youth L</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="Youth XL" class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Youth XL</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Information Section -->
                    <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Product Details</h4>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                    <input type="file" name="product_image" accept="image/*" 
                                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                                    <p class="text-xs text-gray-500 mt-2">Accepted formats: JPG, PNG, GIF, WebP. Max size: 5MB</p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" rows="4" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200"
                                          placeholder="Enter product description..."></textarea>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="is_featured" id="is_featured" 
                                       class="h-5 w-5 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                                <label for="is_featured" class="text-sm font-medium text-gray-900">Featured Product</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-4 pt-6">
                        <button type="button" onclick="closeCreateModal()" 
                                class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-medium">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                            <i class="fas fa-plus mr-2"></i>Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Product</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <!-- Basic Information Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
                        <h4 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            Basic Information
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                                <input type="text" name="name" id="edit_name" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                       placeholder="Enter product name">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Category *</label>
                                <select name="category" id="edit_category" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                    <option value="">Select Category</option>
                                    <option value="Cricket">Cricket</option>
                                    <option value="Football">Football</option>
                                    <option value="Rugby">Rugby</option>
                                    <option value="Tennis">Tennis</option>
                                    <option value="Badminton">Badminton</option>
                                    <option value="Volleyball">Volleyball</option>
                                    <option value="Basketball">Basketball</option>
                                    <option value="Hockey">Hockey</option>
                                    <option value="Swimming">Swimming</option>
                                    <option value="Athletics">Athletics</option>
                                    <option value="Table Tennis">Table Tennis</option>
                                    <option value="Karate">Karate</option>
                                    <option value="Boxing">Boxing</option>
                                    <option value="Weightlifting">Weightlifting</option>
                                    <option value="Cycling">Cycling</option>
                                    <option value="Golf">Golf</option>
                                </select>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Sport Type *</label>
                                <select name="sport_type" id="edit_sport_type" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                                    <option value="">Select Sport Type</option>
                                    <!-- Cricket -->
                                    <option value="Sri Lanka Cricket">Sri Lanka Cricket</option>
                                    <option value="IPL">IPL</option>
                                    <option value="ICC">ICC</option>
                                    <option value="Test Cricket">Test Cricket</option>
                                    <option value="ODI Cricket">ODI Cricket</option>
                                    <option value="T20 Cricket">T20 Cricket</option>
                                    <!-- Football -->
                                    <option value="Sri Lanka Premier League">Sri Lanka Premier League</option>
                                    <option value="FIFA">FIFA</option>
                                    <option value="AFC">AFC</option>
                                    <option value="SAFF">SAFF</option>
                                    <!-- Rugby -->
                                    <option value="Sri Lanka Rugby">Sri Lanka Rugby</option>
                                    <option value="Asia Rugby">Asia Rugby</option>
                                    <option value="World Rugby">World Rugby</option>
                                    <!-- Tennis -->
                                    <option value="Sri Lanka Tennis">Sri Lanka Tennis</option>
                                    <option value="ATP">ATP</option>
                                    <option value="WTA">WTA</option>
                                    <option value="Grand Slam">Grand Slam</option>
                                    <!-- Badminton -->
                                    <option value="Sri Lanka Badminton">Sri Lanka Badminton</option>
                                    <option value="BWF">BWF</option>
                                    <option value="Asia Badminton">Asia Badminton</option>
                                    <!-- Volleyball -->
                                    <option value="Sri Lanka Volleyball">Sri Lanka Volleyball</option>
                                    <option value="FIVB">FIVB</option>
                                    <option value="Asia Volleyball">Asia Volleyball</option>
                                    <!-- Basketball -->
                                    <option value="Sri Lanka Basketball">Sri Lanka Basketball</option>
                                    <option value="FIBA">FIBA</option>
                                    <option value="Asia Basketball">Asia Basketball</option>
                                    <!-- Hockey -->
                                    <option value="Sri Lanka Hockey">Sri Lanka Hockey</option>
                                    <option value="FIH">FIH</option>
                                    <option value="Asia Hockey">Asia Hockey</option>
                                    <!-- Swimming -->
                                    <option value="Sri Lanka Swimming">Sri Lanka Swimming</option>
                                    <option value="FINA">FINA</option>
                                    <option value="Asia Swimming">Asia Swimming</option>
                                    <!-- Athletics -->
                                    <option value="Sri Lanka Athletics">Sri Lanka Athletics</option>
                                    <option value="IAAF">IAAF</option>
                                    <option value="Asia Athletics">Asia Athletics</option>
                                    <!-- Table Tennis -->
                                    <option value="Sri Lanka Table Tennis">Sri Lanka Table Tennis</option>
                                    <option value="ITTF">ITTF</option>
                                    <option value="Asia Table Tennis">Asia Table Tennis</option>
                                    <!-- Martial Arts -->
                                    <option value="Sri Lanka Karate">Sri Lanka Karate</option>
                                    <option value="WKF">WKF</option>
                                    <option value="Asia Karate">Asia Karate</option>
                                    <option value="Sri Lanka Boxing">Sri Lanka Boxing</option>
                                    <option value="AIBA">AIBA</option>
                                    <option value="Asia Boxing">Asia Boxing</option>
                                    <!-- Weightlifting -->
                                    <option value="Sri Lanka Weightlifting">Sri Lanka Weightlifting</option>
                                    <option value="IWF">IWF</option>
                                    <option value="Asia Weightlifting">Asia Weightlifting</option>
                                    <!-- Cycling -->
                                    <option value="Sri Lanka Cycling">Sri Lanka Cycling</option>
                                    <option value="UCI">UCI</option>
                                    <option value="Asia Cycling">Asia Cycling</option>
                                    <!-- Golf -->
                                    <option value="Sri Lanka Golf">Sri Lanka Golf</option>
                                    <option value="R&A">R&A</option>
                                    <option value="USGA">USGA</option>
                                    <option value="Asia Golf">Asia Golf</option>
                                </select>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price (Rs.) *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rs.</span>
                                    <input type="number" name="price" id="edit_price" step="0.01" required 
                                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200"
                                           placeholder="0.00">
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity</label>
                                <input type="number" name="stock_quantity" id="edit_stock_quantity" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all duration-200"
                                       placeholder="Enter stock quantity">
                            </div>
                        </div>
                    </div>
                        
                        <!-- Colors Selection -->
                        <div class="flex-1 space-y-4">
                            <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Colors *</h4>
                            <div class="grid grid-cols-3 gap-3 max-h-64 overflow-y-auto p-2 border border-gray-200 rounded-md">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Navy Blue" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Navy Blue</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Red" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Red</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="White" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">White</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Black" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Black</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Gold" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Gold</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Purple" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Purple</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Blue" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Blue</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Green" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Green</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Orange" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Orange</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Yellow" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Yellow</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Gray" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Gray</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Silver" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Silver</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Pink" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Pink</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Brown" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Brown</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Maroon" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Maroon</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Teal" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Teal</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Cyan" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Cyan</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Lime" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Lime</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Indigo" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Indigo</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="colors[]" value="Violet" class="edit-color-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Violet</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Sizes Selection -->
                        <div class="flex-1 space-y-4">
                            <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Sizes *</h4>
                            <div class="grid grid-cols-2 gap-3 max-h-64 overflow-y-auto p-2 border border-gray-200 rounded-md">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="XS" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">XS</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="S" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">S</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="M" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">M</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="L" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">L</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="XL" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">XL</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="XXL" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">XXL</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="XXXL" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">XXXL</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="Youth S" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Youth S</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="Youth M" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Youth M</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="Youth L" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Youth L</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="sizes[]" value="Youth XL" class="edit-size-checkbox h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                                    <span class="text-sm text-gray-700">Youth XL</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
                        <div class="mb-2">
                            <img id="edit_current_image" src="" alt="Current product image" class="h-20 w-20 object-cover rounded-lg border">
                        </div>
                        <input type="file" name="product_image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image. Accepted formats: JPG, PNG, GIF, WebP. Max size: 5MB</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="edit_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="edit_is_featured" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="edit_is_featured" class="ml-2 block text-sm text-gray-900">Featured Product</label>
                    </div>
                    
                    <div class="flex justify-end space-x-4 pt-6">
                        <button type="button" onclick="closeEditModal()" 
                                class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 font-medium">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                            <i class="fas fa-save mr-2"></i>Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
        }

        function closeCreateModal() {
            document.getElementById('createModal').classList.add('hidden');
        }

        function openEditModal(product) {
            try {
                document.getElementById('edit_id').value = product.id || '';
                document.getElementById('edit_name').value = product.name || '';
                document.getElementById('edit_category').value = product.category || '';
                document.getElementById('edit_sport_type').value = product.sport_type || '';
                document.getElementById('edit_price').value = product.price || '';
                document.getElementById('edit_stock_quantity').value = product.stock_quantity || '';
                document.getElementById('edit_description').value = product.description || '';
            
            // Handle colors and sizes for checkboxes
            const colorCheckboxes = document.querySelectorAll('.edit-color-checkbox');
            const sizeCheckboxes = document.querySelectorAll('.edit-size-checkbox');
            
            // Clear previous selections
            colorCheckboxes.forEach(checkbox => checkbox.checked = false);
            sizeCheckboxes.forEach(checkbox => checkbox.checked = false);
            
            // Set colors
            if (product.colors) {
                try {
                    const colors = JSON.parse(product.colors);
                    colors.forEach(color => {
                        const checkbox = document.querySelector(`.edit-color-checkbox[value="${color}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                } catch (e) {
                    console.error('Error parsing colors:', e);
                }
            }
            
            // Set sizes
            if (product.sizes) {
                try {
                    const sizes = JSON.parse(product.sizes);
                    sizes.forEach(size => {
                        const checkbox = document.querySelector(`.edit-size-checkbox[value="${size}"]`);
                        if (checkbox) checkbox.checked = true;
                    });
                } catch (e) {
                    console.error('Error parsing sizes:', e);
                }
            }
            
            document.getElementById('edit_is_featured').checked = product.is_featured == 1;
            document.getElementById('edit_current_image').src = '../' + (product.image_url || 'assets/images/products/default.jpg');
            
            document.getElementById('editModal').classList.remove('hidden');
        } catch (e) {
            console.error('Error opening edit modal:', e);
            alert('Error loading product details. Please try again.');
        }
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html> 
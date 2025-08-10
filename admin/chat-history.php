<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

// Check if user is admin
if (!isAdmin()) {
    redirect('../login.php');
}

// Get database connection
$db = new Database();
$connection = $db->getConnection();

// Handle search and filters
$search = $_GET['search'] ?? '';
$session_id = $_GET['session_id'] ?? '';
$user_id = $_GET['user_id'] ?? '';
$limit = $_GET['limit'] ?? 50;

// Build query
$query = "SELECT * FROM chat_logs WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (message LIKE ? OR response LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($session_id) {
    $query .= " AND session_id = ?";
    $params[] = $session_id;
}

if ($user_id) {
    $query .= " AND user_id = ?";
    $params[] = $user_id;
}

$query .= " ORDER BY created_at DESC LIMIT ?";
$params[] = $limit;

// Get chat logs
$stmt = $connection->prepare($query);
$stmt->execute($params);
$chat_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total_logs,
    COUNT(DISTINCT session_id) as unique_sessions,
    COUNT(DISTINCT user_id) as unique_users,
    COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 END) as recent_activity
FROM chat_logs";
$stats_stmt = $connection->query($stats_query);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat History - <?php echo SITE_NAME; ?> Admin</title>
    
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
    <header class="bg-white shadow-lg">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="dashboard.php" class="flex items-center space-x-2">
                        <img src="../logo.jpg" alt="<?php echo SITE_NAME; ?>" class="h-8 w-auto">
                    </a>
                </div>

                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="dashboard.php" class="text-gray-700 hover:text-primary transition-colors">Dashboard</a>
                    <a href="products.php" class="text-gray-700 hover:text-primary transition-colors">Products</a>
                    <a href="orders.php" class="text-gray-700 hover:text-primary transition-colors">Orders</a>
                    <a href="users.php" class="text-gray-700 hover:text-primary transition-colors">Users</a>
                    <a href="chat-history.php" class="text-primary font-semibold">Chat History</a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <a href="../logout.php" class="text-gray-700 hover:text-primary transition-colors">Logout</a>
                </div>
            </div>
        </nav>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Chat History</h1>
            <p class="text-gray-600">View and analyze chatbot interactions and responses</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <i class="fas fa-comments text-blue-500 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Total Logs</p>
                        <p class="text-2xl font-bold"><?php echo number_format($stats['total_logs']); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <i class="fas fa-users text-green-500 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Unique Users</p>
                        <p class="text-2xl font-bold"><?php echo number_format($stats['unique_users']); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <i class="fas fa-link text-purple-500 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Sessions</p>
                        <p class="text-2xl font-bold"><?php echo number_format($stats['unique_sessions']); ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <i class="fas fa-clock text-orange-500 text-2xl mr-3"></i>
                    <div>
                        <p class="text-sm text-gray-600">Recent (24h)</p>
                        <p class="text-2xl font-bold"><?php echo number_format($stats['recent_activity']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search messages..." 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Session ID</label>
                    <input type="text" name="session_id" value="<?php echo htmlspecialchars($session_id); ?>" 
                           placeholder="Enter session ID..." 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">User ID</label>
                    <input type="number" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>" 
                           placeholder="Enter user ID..." 
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Limit</label>
                    <select name="limit" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                        <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo $limit == 100 ? 'selected' : ''; ?>>100</option>
                        <option value="200" <?php echo $limit == 200 ? 'selected' : ''; ?>>200</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Chat Logs Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-semibold">Chat Logs (<?php echo count($chat_logs); ?> results)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($chat_logs)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No chat logs found matching your criteria.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($chat_logs as $log): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo htmlspecialchars($log['id']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $log['user_id'] ? htmlspecialchars($log['user_id']) : 'Anonymous'; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <span class="font-mono text-xs"><?php echo htmlspecialchars($log['session_id']); ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                        <div class="truncate" title="<?php echo htmlspecialchars($log['message']); ?>">
                                            <?php echo htmlspecialchars($log['message'] ?: 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 max-w-xs">
                                        <div class="truncate" title="<?php echo htmlspecialchars($log['response']); ?>">
                                            <?php echo htmlspecialchars($log['response'] ?: 'N/A'); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                   <?php echo $log['message_type'] === 'user' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                            <?php echo htmlspecialchars($log['message_type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y g:i A', strtotime($log['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Export Options -->
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Export Options</h3>
            <div class="flex space-x-4">
                <a href="export-chat-logs.php?format=csv<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $session_id ? '&session_id=' . urlencode($session_id) : ''; ?><?php echo $user_id ? '&user_id=' . urlencode($user_id) : ''; ?>" 
                   class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fas fa-download mr-2"></i>Export to CSV
                </a>
                <a href="export-chat-logs.php?format=json<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo $session_id ? '&session_id=' . urlencode($session_id) : ''; ?><?php echo $user_id ? '&user_id=' . urlencode($user_id) : ''; ?>" 
                   class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-code mr-2"></i>Export to JSON
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh every 30 seconds
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html> 
<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Size Guide - Dream Wear</title>
    
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
                    <a href="orders.php" class="relative text-gray-700 hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-primary transition-colors">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="orders.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="flex items-center space-x-4">
                        <a href="login.php" class="text-gray-700 hover:text-primary transition-colors">Login</a>
                        <a href="register.php" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Register</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Size Guide</h1>
            <p class="text-xl text-gray-600">
                Find your perfect fit with our comprehensive size guide. Learn how to measure yourself and find the right size for your jersey.
            </p>
        </div>

        <!-- How to Measure -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">How to Measure</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Step-by-Step Instructions</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">1</div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Chest Measurement</h4>
                                <p class="text-gray-600 text-sm">Measure around the fullest part of your chest, keeping the tape horizontal.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">2</div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Waist Measurement</h4>
                                <p class="text-gray-600 text-sm">Measure around your natural waistline, keeping the tape comfortably loose.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">3</div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Hip Measurement</h4>
                                <p class="text-gray-600 text-sm">Measure around the fullest part of your hips, keeping the tape horizontal.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-3">
                            <div class="bg-primary text-white w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">4</div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Length Measurement</h4>
                                <p class="text-gray-600 text-sm">Measure from the top of your shoulder to where you want the jersey to end.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <div class="bg-gray-100 rounded-lg p-6 mb-4">
                        <i class="fas fa-ruler text-4xl text-primary mb-4"></i>
                        <h4 class="font-semibold text-gray-900 mb-2">Measurement Tips</h4>
                        <ul class="text-sm text-gray-600 text-left space-y-2">
                            <li>• Use a flexible measuring tape</li>
                            <li>• Don't pull the tape too tight</li>
                            <li>• Measure over light clothing</li>
                            <li>• Keep the tape horizontal</li>
                            <li>• Have someone help you measure</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Size Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Men's Size Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Men's Size Chart</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left font-semibold">Size</th>
                                <th class="px-4 py-2 text-left font-semibold">Chest (in)</th>
                                <th class="px-4 py-2 text-left font-semibold">Waist (in)</th>
                                <th class="px-4 py-2 text-left font-semibold">Length (in)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 font-semibold">S</td>
                                <td class="px-4 py-2">34-36</td>
                                <td class="px-4 py-2">28-30</td>
                                <td class="px-4 py-2">26-27</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold">M</td>
                                <td class="px-4 py-2">38-40</td>
                                <td class="px-4 py-2">32-34</td>
                                <td class="px-4 py-2">27-28</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold">L</td>
                                <td class="px-4 py-2">42-44</td>
                                <td class="px-4 py-2">36-38</td>
                                <td class="px-4 py-2">28-29</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold">XL</td>
                                <td class="px-4 py-2">46-48</td>
                                <td class="px-4 py-2">40-42</td>
                                <td class="px-4 py-2">29-30</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold">2XL</td>
                                <td class="px-4 py-2">50-52</td>
                                <td class="px-4 py-2">44-46</td>
                                <td class="px-4 py-2">30-31</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold">3XL</td>
                                <td class="px-4 py-2">54-56</td>
                                <td class="px-4 py-2">48-50</td>
                                <td class="px-4 py-2">31-32</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Women's Size Chart -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Women's Size Chart</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-2 text-left font-semibold">Size</th>
                                <th class="px-4 py-2 text-left font-semibold">Chest (in)</th>
                                <th class="px-4 py-2 text-left font-semibold">Waist (in)</th>
                                <th class="px-4 py-2 text-left font-semibold">Length (in)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 font-semibold">XS</td>
                                <td class="px-4 py-2">30-32</td>
                                <td class="px-4 py-2">24-26</td>
                                <td class="px-4 py-2">24-25</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold">S</td>
                                <td class="px-4 py-2">32-34</td>
                                <td class="px-4 py-2">26-28</td>
                                <td class="px-4 py-2">25-26</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold">M</td>
                                <td class="px-4 py-2">34-36</td>
                                <td class="px-4 py-2">28-30</td>
                                <td class="px-4 py-2">26-27</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold">L</td>
                                <td class="px-4 py-2">36-38</td>
                                <td class="px-4 py-2">30-32</td>
                                <td class="px-4 py-2">27-28</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold">XL</td>
                                <td class="px-4 py-2">38-40</td>
                                <td class="px-4 py-2">32-34</td>
                                <td class="px-4 py-2">28-29</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 font-semibold">2XL</td>
                                <td class="px-4 py-2">40-42</td>
                                <td class="px-4 py-2">34-36</td>
                                <td class="px-4 py-2">29-30</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Youth Size Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Youth Size Chart</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-2 text-left font-semibold">Size</th>
                            <th class="px-4 py-2 text-left font-semibold">Age</th>
                            <th class="px-4 py-2 text-left font-semibold">Chest (in)</th>
                            <th class="px-4 py-2 text-left font-semibold">Length (in)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="px-4 py-2 font-semibold">2T</td>
                            <td class="px-4 py-2">2 years</td>
                            <td class="px-4 py-2">20-22</td>
                            <td class="px-4 py-2">14-15</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-semibold">3T</td>
                            <td class="px-4 py-2">3 years</td>
                            <td class="px-4 py-2">22-24</td>
                            <td class="px-4 py-2">15-16</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-semibold">4T</td>
                            <td class="px-4 py-2">4 years</td>
                            <td class="px-4 py-2">24-26</td>
                            <td class="px-4 py-2">16-17</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-semibold">5T</td>
                            <td class="px-4 py-2">5 years</td>
                            <td class="px-4 py-2">26-28</td>
                            <td class="px-4 py-2">17-18</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-semibold">6</td>
                            <td class="px-4 py-2">6 years</td>
                            <td class="px-4 py-2">28-30</td>
                            <td class="px-4 py-2">18-19</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-semibold">7</td>
                            <td class="px-4 py-2">7 years</td>
                            <td class="px-4 py-2">30-32</td>
                            <td class="px-4 py-2">19-20</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-semibold">8</td>
                            <td class="px-4 py-2">8 years</td>
                            <td class="px-4 py-2">32-34</td>
                            <td class="px-4 py-2">20-21</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-semibold">10</td>
                            <td class="px-4 py-2">10 years</td>
                            <td class="px-4 py-2">34-36</td>
                            <td class="px-4 py-2">21-22</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2 font-semibold">12</td>
                            <td class="px-4 py-2">12 years</td>
                            <td class="px-4 py-2">36-38</td>
                            <td class="px-4 py-2">22-23</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Fit Guide -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-tshirt text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Regular Fit</h3>
                </div>
                <p class="text-gray-600 text-center">
                    Standard fit with comfortable room for movement. Perfect for everyday wear and casual use.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-running text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Athletic Fit</h3>
                </div>
                <p class="text-gray-600 text-center">
                    Tapered fit designed for athletes. Snug in the chest and arms with room to move.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="bg-accent text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Loose Fit</h3>
                </div>
                <p class="text-gray-600 text-center">
                    Relaxed fit with extra room for comfort. Great for layering or a more casual look.
                </p>
            </div>
        </div>

        
        
    </div>

    <?php include 'includes/chat-widget.php'; ?>
    <?php include 'includes/footer.php'; ?>
</body>
</html> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - Smart Expense Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/tailwind.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .navbar-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
        }
        .logo-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .sidebar-gradient {
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Navbar -->
    <nav class="navbar-gradient shadow-xl sticky top-0 z-50 border-b-2 border-blue-400">
        <div class="px-4 lg:px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Left: Logo & Menu Toggle -->
                <div class="flex items-center space-x-4">
                    <button id="menuToggle" class="lg:hidden text-white hover:text-blue-200 transition focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="flex items-center space-x-3">
                        <div class="bg-white bg-opacity-20 rounded-xl p-2 backdrop-blur-sm">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="hidden sm:block">
                            <h1 class="text-xl lg:text-2xl font-bold text-white logo-shadow">XpensePro</h1>
                            <p class="text-blue-200 text-xs font-medium">Smart Financial Management</p>
                        </div>
                    </div>
                </div>
                
                <!-- Right: User Info & Logout -->
                <div class="flex items-center space-x-3 lg:space-x-6">
                    <!-- User Info -->
                    <div class="hidden md:flex items-center space-x-3 bg-white bg-opacity-10 rounded-full px-4 py-2 backdrop-blur-sm">
                        <div class="bg-white bg-opacity-20 rounded-full p-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="text-white">
                            <p class="font-semibold text-sm">Welcome back</p>
                            <p class="text-blue-200 text-xs"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                        </div>
                    </div>
                    <!-- Logout Button -->
                    <a href="logout.php" class="flex items-center space-x-2 bg-white bg-opacity-10 hover:bg-opacity-20 text-white px-3 lg:px-4 py-2 rounded-full transition duration-300 border border-white border-opacity-20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="hidden sm:inline font-medium text-sm">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out sidebar-gradient text-white w-64 min-h-screen pt-20 lg:pt-0 z-40 shadow-2xl">
            <div class="p-6 border-b border-gray-700">
                <p class="text-gray-400 text-sm font-medium uppercase tracking-wide">Navigation</p>
            </div>
            <nav class="mt-6 px-3">
                <a href="index.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-blue-600 hover:shadow-lg transition group <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'bg-blue-600 shadow-lg' : ''; ?>">
                    <div class="bg-blue-500 bg-opacity-30 rounded-lg p-2 mr-3 group-hover:bg-opacity-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="income.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-green-600 hover:shadow-lg transition group <?php echo basename($_SERVER['PHP_SELF']) === 'income.php' ? 'bg-green-600 shadow-lg' : ''; ?>">
                    <div class="bg-green-500 bg-opacity-30 rounded-lg p-2 mr-3 group-hover:bg-opacity-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="font-medium">Income</span>
                </a>
                <a href="expense.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-red-600 hover:shadow-lg transition group <?php echo basename($_SERVER['PHP_SELF']) === 'expense.php' ? 'bg-red-600 shadow-lg' : ''; ?>">
                    <div class="bg-red-500 bg-opacity-30 rounded-lg p-2 mr-3 group-hover:bg-opacity-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                    </div>
                    <span class="font-medium">Expenses</span>
                </a>
                <a href="categories.php" class="flex items-center px-4 py-3 mb-2 rounded-lg hover:bg-yellow-600 hover:shadow-lg transition group <?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'bg-yellow-600 shadow-lg' : ''; ?>">
                    <div class="bg-yellow-500 bg-opacity-30 rounded-lg p-2 mr-3 group-hover:bg-opacity-50 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <span class="font-medium">Categories</span>
                </a>
            </nav>
            
            <!-- Sidebar Footer -->
            <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-700">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-500 rounded-full p-2">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1 lg:block hidden">
                        <p class="text-sm font-semibold text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                        <p class="text-xs text-gray-400">User Account</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Overlay for mobile -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

        <!-- Main Content -->
        <main class="flex-1 p-4 lg:p-8 min-h-screen">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - Smart Expense Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/tailwind.css">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 64px;
            z-index: 50;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-x: hidden;
            box-shadow: 2px 0 12px rgba(0,0,0,0.1);
        }
        .sidebar:hover {
            width: 240px;
        }
        .sidebar-label {
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
            white-space: nowrap;
        }
        .sidebar:hover .sidebar-label {
            opacity: 1;
            visibility: visible;
        }
        .nav-item {
            position: relative;
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: #3b82f6;
        }
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
                width: 240px;
            }
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Top Navbar -->
    <nav class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-40 shadow-sm">
        <div class="px-4 lg:px-6 py-3">
            <div class="flex items-center justify-between">
                <!-- Left: Menu Toggle & Logo -->
                <div class="flex items-center space-x-4">
                    <button id="menuToggle" class="lg:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="flex items-center space-x-3">
                        <div class="bg-gradient-to-br from-blue-600 to-blue-500 rounded-lg p-2 shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-lg font-bold text-gray-800">XpensePro</h1>
                            <p class="text-xs text-gray-500 hidden sm:block">Financial Dashboard</p>
                        </div>
                    </div>
                </div>
                
                <!-- Right: User Info & Logout -->
                <div class="flex items-center space-x-4">
                    <!-- User Info -->
                    <div class="hidden md:flex items-center space-x-3 bg-gray-50 rounded-full px-4 py-2 border border-gray-200">
                        <div class="bg-blue-100 rounded-full p-1.5">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <p class="font-semibold text-gray-700"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                            <p class="text-xs text-gray-500">Account</p>
                        </div>
                    </div>
                    <!-- Logout Button -->
                    <a href="logout.php" class="flex items-center space-x-2 bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg transition border border-red-200 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <aside id="sidebar" class="sidebar">
        <!-- Logo Area -->
        <div class="flex items-center justify-center h-16 border-b border-gray-700">
            <div class="bg-blue-500 rounded-lg p-2">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        
        <nav class="mt-6 px-3">
            <a href="index.php" class="nav-item flex items-center px-3 py-3 mb-2 rounded-lg hover:bg-gray-700 transition group <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active bg-gray-700' : ''; ?>">
                <div class="flex items-center justify-center w-8 h-8">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <span class="sidebar-label ml-4 text-gray-200 font-medium">Dashboard</span>
            </a>
            
            <a href="income.php" class="nav-item flex items-center px-3 py-3 mb-2 rounded-lg hover:bg-gray-700 transition group <?php echo basename($_SERVER['PHP_SELF']) === 'income.php' ? 'active bg-gray-700' : ''; ?>">
                <div class="flex items-center justify-center w-8 h-8">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="sidebar-label ml-4 text-gray-200 font-medium">Income</span>
            </a>
            
            <a href="expense.php" class="nav-item flex items-center px-3 py-3 mb-2 rounded-lg hover:bg-gray-700 transition group <?php echo basename($_SERVER['PHP_SELF']) === 'expense.php' ? 'active bg-gray-700' : ''; ?>">
                <div class="flex items-center justify-center w-8 h-8">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <span class="sidebar-label ml-4 text-gray-200 font-medium">Expenses</span>
            </a>
            
            <a href="categories.php" class="nav-item flex items-center px-3 py-3 mb-2 rounded-lg hover:bg-gray-700 transition group <?php echo basename($_SERVER['PHP_SELF']) === 'categories.php' ? 'active bg-gray-700' : ''; ?>">
                <div class="flex items-center justify-center w-8 h-8">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <span class="sidebar-label ml-4 text-gray-200 font-medium">Categories</span>
            </a>
        </nav>
        
        <!-- Sidebar Footer -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-700">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 bg-blue-500 rounded-full">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div class="sidebar-label ml-3">
                    <p class="text-sm font-medium text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p class="text-xs text-gray-400">Active User</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Overlay for mobile -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden lg:hidden"></div>

    <!-- Main Content -->
    <main class="pt-16 lg:ml-16 min-h-screen p-4 lg:p-8 transition-all">
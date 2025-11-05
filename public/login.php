<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (login($username, $password)) {
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Expense Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-left {
            background: linear-gradient(135deg, #2563eb 0%, #1e3a8a 100%);
        }
        .trust-badge img {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 2px solid #fff;
            object-fit: cover;
            margin-left: -10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .trust-badge .extra {
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #fff;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 2px solid #fff;
            margin-left: -10px;
            font-size: 0.95rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="w-full max-w-4xl mx-auto bg-white rounded-3xl shadow-2xl flex overflow-hidden">
        <!-- Left Side -->
        <div class="hidden md:flex flex-col justify-between gradient-left p-10 w-1/2 text-white">
            <div>
                <div class="flex items-center mb-8">
                    <div class="bg-white bg-opacity-20 rounded-xl p-2 mr-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold tracking-wide">XpensePro</span>
                </div>
                <h2 class="text-4xl font-bold mb-4">Welcome Back!</h2>
                <p class="text-lg mb-8 opacity-90">To stay connected with us please login with your personal info</p>
            </div>
            <div>
                <p class="text-sm mb-3 opacity-80">Trusted by professionals worldwide</p>
                <div class="flex items-center trust-badge">
                    <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="user1">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="user2">
                    <img src="https://randomuser.me/api/portraits/men/65.jpg" alt="user3">
                    <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="user4">
                    <span class="extra">+20</span>
                    <span class="ml-3 text-white text-xs opacity-80">and many more...</span>
                </div>
            </div>
        </div>
        <!-- Right Side -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-bold text-blue-900 mb-2" style="font-family: serif;">Welcome</h2>
                    <p class="text-gray-500">Login to your account to continue</p>
                </div>
                <?php if ($error): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center text-sm">
                        <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
                <form method="POST" class="space-y-6">
                    <div>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            class="w-full px-4 py-3 bg-blue-50 border-2 border-blue-100 rounded-full focus:outline-none text-gray-800 placeholder-gray-400"
                            placeholder="Enter your username or email"
                        >
                    </div>
                    <div>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full px-4 py-3 bg-blue-50 border-2 border-blue-100 rounded-full focus:outline-none text-gray-800 placeholder-gray-400 pr-12"
                                placeholder="Enter your password"
                            >
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Forget your password?</a>
                    </div>
                    <button 
                        type="submit" 
                        class="w-full py-3 rounded-full text-white font-semibold text-base shadow-lg bg-gradient-to-r from-blue-600 to-blue-400 hover:from-blue-700 hover:to-blue-500 transition"
                    >
                        SIGN IN
                    </button>
                </form>
                <div class="mt-8 text-center">
                    <p class="text-gray-600 text-sm">
                        Don't have an account? 
                        <a href="signup.php" class="text-blue-600 hover:text-blue-700 font-semibold">Sign up here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }
    </script>
</body>
</html>
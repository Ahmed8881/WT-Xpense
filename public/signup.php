<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $result = registerUser($username, $password);
        if (is_array($result) && $result['success']) {
            $success = 'Account created successfully! Redirecting to dashboard...';
            $_SESSION['user_id'] = $result['user_id'];
            $_SESSION['username'] = $username;
            header('refresh:2;url=index.php');
        } else {
            $error = is_array($result) ? $result['message'] : 'Username already exists or registration failed';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Smart Expense Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            background: #fff;
        }
        .curve-bg {
            position: absolute;
            width: 100vw;
            height: 100vh;
            top: 0;
            left: 0;
            z-index: 0;
            pointer-events: none;
        }
        @media (max-width: 768px) {
            html, body {
                overflow: auto;
            }
            .curve-bg {
                display: none;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative">
    <!-- Decorative Curves -->
    <svg class="curve-bg" viewBox="0 0 1440 900" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path fill="#2563eb" fill-opacity="0.08" d="M0,600 C400,900 1040,300 1440,700 L1440,0 L0,0 Z"/>
        <path fill="#2563eb" fill-opacity="0.12" d="M0,800 C600,900 1240,400 1440,900 L1440,0 L0,0 Z"/>
        <circle cx="1200" cy="200" r="120" fill="#60a5fa" fill-opacity="0.10"/>
        <circle cx="200" cy="700" r="80" fill="#2563eb" fill-opacity="0.07"/>
    </svg>
    <div class="w-full max-w-lg mx-auto bg-white rounded-3xl shadow-2xl p-10 relative z-10">
        <div class="flex flex-col items-center mb-8">
            <div class="bg-gradient-to-br from-blue-600 to-blue-400 rounded-full w-16 h-16 flex items-center justify-center mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="8" r="4" stroke-width="2" stroke="currentColor" fill="#93c5fd"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 20v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-blue-800 mb-2">Create Your Account</h2>
            <p class="text-blue-500 text-center max-w-xs">Sign up to start tracking your expenses and income with ease!</p>
        </div>
        <?php if ($error): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center text-sm">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 flex items-center text-sm">
                <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span><?php echo htmlspecialchars($success); ?></span>
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
                    placeholder="Choose a username"
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
                        placeholder="Create a password (min 6 characters)"
                    >
                    <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg id="eyeIcon1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div>
                <div class="relative">
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                        class="w-full px-4 py-3 bg-blue-50 border-2 border-blue-100 rounded-full focus:outline-none text-gray-800 placeholder-gray-400 pr-12"
                        placeholder="Re-enter your password"
                    >
                    <button type="button" onclick="togglePassword('confirm_password')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg id="eyeIcon2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <button 
                type="submit" 
                class="w-full py-3 rounded-full text-white font-semibold text-base shadow-lg bg-gradient-to-r from-blue-600 to-blue-400 hover:from-blue-700 hover:to-blue-500 transition"
            >
                CREATE ACCOUNT
            </button>
        </form>
        <div class="mt-8 text-center">
            <p class="text-gray-600 text-sm">
                Already have an account? 
                <a href="login.php" class="text-blue-600 hover:text-blue-700 font-semibold">Sign in here</a>
            </p>
        </div>
    </div>
    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
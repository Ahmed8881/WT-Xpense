<?php
/**
 * Admin User Setup Script
 * Run this file once to create/update admin user
 * Access: http://localhost/Xpense/setup_admin.php
 */

require_once 'config/database.php';

$username = 'admin';
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$status = '';
$statusType = '';

try {
    $conn = getDBConnection();
    
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update existing user
        $stmt->close();
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $hashedPassword, $username);
        
        if ($stmt->execute()) {
            $status = "Admin user password updated successfully!";
            $statusType = "success";
        } else {
            $status = "Error updating admin user: " . $stmt->error;
            $statusType = "error";
        }
    } else {
        // Insert new user
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashedPassword);
        
        if ($stmt->execute()) {
            $status = "Admin user created successfully!";
            $statusType = "success";
        } else {
            $status = "Error creating admin user: " . $stmt->error;
            $statusType = "error";
        }
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    $status = "Database error: " . $e->getMessage();
    $statusType = "error";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Setup - Smart Expense Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-2xl max-w-2xl w-full">
        <?php if ($statusType === 'success'): ?>
            <div class="text-center mb-6">
                <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">✅ Success!</h2>
                <p class="text-lg text-gray-600"><?php echo htmlspecialchars($status); ?></p>
            </div>
        <?php else: ?>
            <div class="text-center mb-6">
                <div class="bg-red-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-2">❌ Error</h2>
                <p class="text-lg text-red-600"><?php echo htmlspecialchars($status); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($statusType === 'success'): ?>
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Login Credentials</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <p><strong>Username:</strong> admin</p>
                            <p><strong>Password:</strong> admin123</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Next Steps</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Delete this setup_admin.php file for security</li>
                                <li>Go to the login page</li>
                                <li>Login with the credentials above</li>
                                <li>You can also create new accounts via signup page</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <a href="public/login.php" class="flex-1 text-center bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg">
                    Go to Login Page →
                </a>
                <a href="public/signup.php" class="flex-1 text-center bg-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-green-700 transition shadow-lg">
                    Sign Up New Account →
                </a>
            </div>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    ⚠️ Remember to delete <code class="bg-gray-200 px-2 py-1 rounded">setup_admin.php</code> for security!
                </p>
            </div>
        <?php else: ?>
            <div class="mt-6 text-center">
                <a href="setup_admin.php" class="inline-block bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Try Again
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

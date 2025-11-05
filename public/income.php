<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

checkAuth();

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $title = $_POST['title'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $date = $_POST['date'] ?? date('Y-m-d');
        
        if (addIncome($title, $amount, $date, $user_id)) {
            $message = 'Income added successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to add income.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'] ?? 0;
        if (deleteIncome($id, $user_id)) {
            $message = 'Income deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to delete income.';
            $messageType = 'error';
        }
    }
}

// Get all incomes
$incomes = getAllIncomes($user_id);
$totalIncome = getTotalIncome($user_id);

// For progress bar (example goal)
$incomeGoal = 50000;
$incomePercent = min(100, ($totalIncome / $incomeGoal) * 100);

$pageTitle = 'Income Management';
include 'includes/header.php';
?>

<style>
.card-analytic-bar {
    width: 100%;
    height: 10px;
    margin-top: 8px;
    background: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
    position: relative;
}
.card-analytic-bar-inner {
    height: 100%;
    border-radius: 9999px;
    transition: width 0.6s cubic-bezier(.4,0,.2,1);
}
</style>

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200">
    <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Income Management</h1>
                <p class="text-gray-600 mt-2">Track and manage your income sources</p>
            </div>
            <div>
                <a href="logout.php" class="inline-block bg-gray-800 text-white px-5 py-2 rounded-lg font-semibold shadow hover:bg-gray-900 transition">Logout</a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($message): ?>
            <div class="p-4 rounded-lg shadow <?php echo $messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'; ?>">
                <div class="flex items-center">
                    <?php if ($messageType === 'success'): ?>
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    <?php else: ?>
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    <?php endif; ?>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Left Sidebar - Forms & Summary -->
            <div class="xl:col-span-1 space-y-8">
                <!-- Total Income Summary Card -->
                <div class="bg-gradient-to-br from-blue-100 via-gray-100 to-green-100 rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-gray-500 text-sm font-semibold uppercase tracking-wide">Total Income</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-1">$<?php echo number_format($totalIncome, 2); ?></h3>
                        </div>
                        <div class="bg-blue-200 p-4 rounded-xl">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <!-- Goal Progress Bar -->
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-xs text-gray-500 font-medium">Annual Goal</span>
                            <span class="text-xs text-gray-700 font-semibold"><?php echo number_format($incomePercent, 1); ?>%</span>
                        </div>
                        <div class="card-analytic-bar">
                            <div class="card-analytic-bar-inner bg-gradient-to-r from-blue-400 to-green-400" style="width: <?php echo $incomePercent; ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Goal: $<?php echo number_format($incomeGoal, 2); ?></p>
                    </div>
                </div>

                <!-- Add Income Form -->
                <div class="bg-gradient-to-br from-gray-50 via-blue-50 to-green-50 rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-100 p-3 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Add New Income</h3>
                    </div>
                    
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="add">
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm">Income Title</label>
                            <input 
                                type="text" 
                                name="title" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none transition-all"
                                placeholder="e.g., Salary, Freelance, Bonus"
                            >
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm">Amount ($)</label>
                            <input 
                                type="number" 
                                name="amount" 
                                step="0.01" 
                                min="0" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none transition-all"
                                placeholder="0.00"
                            >
                        </div>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2 text-sm">Date</label>
                            <input 
                                type="date" 
                                name="date" 
                                value="<?php echo date('Y-m-d'); ?>"
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none transition-all"
                            >
                        </div>
                        
                        <button 
                            type="submit" 
                            class="w-full bg-blue-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-blue-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                        >
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Income
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right Content - Income History Table -->
            <div class="xl:col-span-3">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-2xl font-bold text-gray-800">Income History</h3>
                        <div class="bg-blue-100 px-4 py-2 rounded-xl">
                            <span class="text-sm font-semibold text-blue-700"><?php echo count($incomes); ?> Records</span>
                        </div>
                    </div>
                    
                    <?php if (empty($incomes)): ?>
                        <div class="text-center py-16">
                            <div class="bg-blue-100 rounded-full w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                                <svg class="w-12 h-12 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-700 mb-2">No Income Records Yet</h4>
                            <p class="text-gray-500 mb-6">Start by adding your first income source using the form on the left.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto rounded-xl shadow">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-blue-50 border-b border-gray-200">
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider rounded-l-xl">Date</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Title</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider rounded-r-xl">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($incomes as $income): ?>
                                        <tr class="hover:bg-blue-50 transition-colors group">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                <?php echo date('M d, Y', strtotime($income['date'])); ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($income['title']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-blue-700">
                                                $<?php echo number_format($income['amount'], 2); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this income?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $income['id']; ?>">
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium px-3 py-1 rounded-lg hover:bg-red-50 transition">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Table Footer Summary -->
                        <div class="mt-6 pt-6 border-t border-gray-200 flex justify-between items-center">
                            <div class="text-sm text-gray-600">
                                Showing <?php echo count($incomes); ?> income records
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600">Total Income</div>
                                <div class="text-2xl font-bold text-blue-700">$<?php echo number_format($totalIncome, 2); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
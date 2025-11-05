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
        $category_id = $_POST['category_id'] ?? 0;
        $amount = $_POST['amount'] ?? 0;
        $date = $_POST['date'] ?? date('Y-m-d');
        
        if (addExpense($category_id, $amount, $date, $user_id)) {
            $message = 'Expense added successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to add expense.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'] ?? 0;
        if (deleteExpense($id, $user_id)) {
            $message = 'Expense deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to delete expense.';
            $messageType = 'error';
        }
    }
}

// Get all expenses and categories
$expenses = getAllExpenses($user_id);
$categories = getAllCategories($user_id);
$totalExpense = getTotalExpense($user_id);

$pageTitle = 'Expense Management';
include 'includes/header.php';
?>

<style>
.card-analytic-bar {
    width: 100%;
    height: 10px;
    margin-top: 8px;
    background: #f3f4f6;
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

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-gray-100 to-red-50">
    <div class="max-w-7xl mx-auto py-10 px-4 space-y-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Expense Management</h2>
                <p class="text-gray-600 mt-2">Track and manage your expenses</p>
            </div>
            <div>
                <a href="logout.php" class="inline-block bg-gray-800 text-white px-5 py-2 rounded-lg font-semibold shadow hover:bg-gray-900 transition">Logout</a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($message): ?>
            <div class="p-4 rounded-lg shadow <?php echo $messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            <!-- Add Expense Form & Summary -->
            <div class="xl:col-span-1 space-y-8">
                <div class="bg-gradient-to-br from-red-100 via-gray-100 to-orange-100 rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <p class="text-gray-500 text-sm font-semibold uppercase tracking-wide">Total Expense</p>
                            <h3 class="text-3xl font-bold text-gray-900 mt-1">$<?php echo number_format($totalExpense, 2); ?></h3>
                        </div>
                        <div class="bg-red-200 p-4 rounded-xl">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                        </div>
                    </div>
                    <!-- Progress Bar (example, static 60%) -->
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-xs text-gray-500 font-medium">Monthly Limit</span>
                            <span class="text-xs text-gray-700 font-semibold"><?php echo number_format($totalExpense, 2); ?> / 20,000</span>
                        </div>
                        <div class="card-analytic-bar">
                            <div class="card-analytic-bar-inner bg-gradient-to-r from-red-400 to-orange-400" style="width: <?php echo min(100, ($totalExpense/20000)*100); ?>%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Limit: $20,000</p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-gray-50 via-red-50 to-orange-50 rounded-2xl shadow-lg p-8 border border-gray-200">
                    <div class="flex items-center mb-6">
                        <div class="bg-red-100 p-3 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">Add New Expense</h3>
                    </div>
                    
                    <?php if (empty($categories)): ?>
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                            Please <a href="categories.php" class="font-bold underline">add categories</a> first!
                        </div>
                    <?php else: ?>
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="action" value="add">
                            
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Category</label>
                                <select 
                                    name="category_id" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none"
                                >
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>">
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Amount ($)</label>
                                <input 
                                    type="number" 
                                    name="amount" 
                                    step="0.01" 
                                    min="0" 
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none"
                                    placeholder="0.00"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-gray-700 font-semibold mb-2">Date</label>
                                <input 
                                    type="date" 
                                    name="date" 
                                    value="<?php echo date('Y-m-d'); ?>"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none"
                                >
                            </div>
                            
                            <button 
                                type="submit" 
                                class="w-full bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition shadow-lg"
                            >
                                Add Expense
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Expense List Table -->
            <div class="xl:col-span-3">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-800 mb-8">Expense History</h3>
                    
                    <?php if (empty($expenses)): ?>
                        <div class="text-center py-16">
                            <div class="bg-red-100 rounded-full w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                                <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-700 mb-2">No expense records yet</h4>
                            <p class="text-gray-500 mb-6">Add your first expense using the form on the left.</p>
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto rounded-xl shadow">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-red-50 border-b border-gray-200">
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider rounded-l-xl">Date</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Category</th>
                                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 uppercase tracking-wider rounded-r-xl">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($expenses as $expense): ?>
                                        <tr class="hover:bg-red-50 transition-colors group">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                <?php echo date('M d, Y', strtotime($expense['date'])); ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    <?php echo htmlspecialchars($expense['category_name'] ?? 'N/A'); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-red-600">
                                                $<?php echo number_format($expense['amount'], 2); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this expense?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $expense['id']; ?>">
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
                                Showing <?php echo count($expenses); ?> expense records
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-600">Total Expense</div>
                                <div class="text-2xl font-bold text-red-600">$<?php echo number_format($totalExpense, 2); ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
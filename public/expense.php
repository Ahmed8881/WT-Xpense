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

<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Expense Management</h2>
        <p class="text-gray-600 mt-2">Track and manage your expenses</p>
    </div>

    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Expense Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Add New Expense</h3>
                
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
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-gray-600 text-sm">Total Expenses</p>
                        <p class="text-3xl font-bold text-red-600">$<?php echo number_format($totalExpense, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Expense History</h3>
                
                <?php if (empty($expenses)): ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <p class="text-gray-500">No expense records yet. Add your first expense!</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($expenses as $expense): ?>
                                    <tr class="hover:bg-gray-50">
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
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

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

$pageTitle = 'Income Management';
include 'includes/header.php';
?>

<div class="max-w-7xl mx-auto">
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-800">Income Management</h2>
        <p class="text-gray-600 mt-2">Track and manage your income sources</p>
    </div>

    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'success' ? 'bg-green-100 border border-green-400 text-green-700' : 'bg-red-100 border border-red-400 text-red-700'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Income Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Add New Income</h3>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add">
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Title</label>
                        <input 
                            type="text" 
                            name="title" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none"
                            placeholder="e.g., Salary, Freelance"
                        >
                    </div>
                    
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Amount ($)</label>
                        <input 
                            type="number" 
                            name="amount" 
                            step="0.01" 
                            min="0" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none"
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
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none"
                        >
                    </div>
                    
                    <button 
                        type="submit" 
                        class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition shadow-lg"
                    >
                        Add Income
                    </button>
                </form>
                
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="text-center">
                        <p class="text-gray-600 text-sm">Total Income</p>
                        <p class="text-3xl font-bold text-green-600">$<?php echo number_format($totalIncome, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Income List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Income History</h3>
                
                <?php if (empty($incomes)): ?>
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-500">No income records yet. Add your first income!</p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($incomes as $income): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            <?php echo date('M d, Y', strtotime($income['date'])); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($income['title']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-green-600">
                                            $<?php echo number_format($income['amount'], 2); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this income?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $income['id']; ?>">
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

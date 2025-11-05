<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

checkAuth();

$user_id = $_SESSION['user_id'];

// Get dashboard data
$totalIncome = getTotalIncome($user_id);
$totalExpense = getTotalExpense($user_id);
$balance = $totalIncome - $totalExpense;
$todayExpense = getTodayExpense($user_id);

// Get chart data
$monthlyExpenses = getMonthlyExpenseData($user_id);
$comparisonData = getIncomeVsExpenseData($user_id);

// Prepare data for JavaScript
$monthlyLabels = array_map(function($item) {
    return date('M Y', strtotime($item['month'] . '-01'));
}, $monthlyExpenses);

$monthlyValues = array_map(function($item) {
    return floatval($item['total']);
}, $monthlyExpenses);

// Prepare comparison data
$allMonths = [];
foreach ($comparisonData['incomes'] as $inc) {
    $allMonths[$inc['month']] = ['income' => floatval($inc['total']), 'expense' => 0];
}
foreach ($comparisonData['expenses'] as $exp) {
    if (isset($allMonths[$exp['month']])) {
        $allMonths[$exp['month']]['expense'] = floatval($exp['total']);
    } else {
        $allMonths[$exp['month']] = ['income' => 0, 'expense' => floatval($exp['total'])];
    }
}
ksort($allMonths);

$comparisonLabels = array_map(function($month) {
    return date('M Y', strtotime($month . '-01'));
}, array_keys($allMonths));

$comparisonIncomes = array_map(function($item) {
    return $item['income'];
}, array_values($allMonths));

$comparisonExpenses = array_map(function($item) {
    return $item['expense'];
}, array_values($allMonths));

$pageTitle = 'Dashboard';
include 'includes/header.php';
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Income Card -->
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Total Income</p>
                <h3 class="text-3xl font-bold mt-2">$<?php echo number_format($totalIncome, 2); ?></h3>
            </div>
            <div class="bg-white bg-opacity-30 p-3 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Total Expense Card -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm font-medium">Total Expense</p>
                <h3 class="text-3xl font-bold mt-2">$<?php echo number_format($totalExpense, 2); ?></h3>
            </div>
            <div class="bg-white bg-opacity-30 p-3 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Balance</p>
                <h3 class="text-3xl font-bold mt-2">$<?php echo number_format($balance, 2); ?></h3>
            </div>
            <div class="bg-white bg-opacity-30 p-3 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Today's Expense Card -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Today's Expense</p>
                <h3 class="text-3xl font-bold mt-2">$<?php echo number_format($todayExpense, 2); ?></h3>
            </div>
            <div class="bg-white bg-opacity-30 p-3 rounded-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Monthly Expense Bar Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Monthly Expenses</h2>
        <div class="h-80">
            <canvas id="monthlyExpenseChart"></canvas>
        </div>
    </div>

    <!-- Income vs Expense Comparison Chart -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Income vs Expense</h2>
        <div class="h-80">
            <canvas id="comparisonChart"></canvas>
        </div>
    </div>
</div>

<script>
// Inject PHP data into JavaScript
window.monthlyExpenseData = {
    labels: <?php echo json_encode($monthlyLabels); ?>,
    data: <?php echo json_encode($monthlyValues); ?>
};

window.comparisonData = {
    labels: <?php echo json_encode($comparisonLabels); ?>,
    income: <?php echo json_encode($comparisonIncomes); ?>,
    expense: <?php echo json_encode($comparisonExpenses); ?>
};
</script>

<?php include 'includes/footer.php'; ?>

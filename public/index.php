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
$categoryBreakdown = getExpenseCategoryBreakdown($user_id);

// Prepare data for JavaScript
$monthlyLabels = array_map(function($item) {
    return date('M Y', strtotime($item['month'] . '-01'));
}, $monthlyExpenses);

$monthlyValues = array_map(function($item) {
    return floatval($item['total']);
}, $monthlyExpenses);

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

$categoryLabels = array_map(function($item) {
    return $item['category'];
}, $categoryBreakdown);

$categoryValues = array_map(function($item) {
    return floatval($item['total']);
}, $categoryBreakdown);

// Calculate percentage change (mock data - you can implement actual logic)
$incomeChange = 12.5;
$expenseChange = -8.3;
$balanceChange = 24.7;

$pageTitle = 'Dashboard';
include 'includes/header.php';
?>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

.card-animate {
    animation: fadeInUp 0.6s ease-out forwards;
}

.card-animate:nth-child(1) { animation-delay: 0.1s; }
.card-animate:nth-child(2) { animation-delay: 0.2s; }
.card-animate:nth-child(3) { animation-delay: 0.3s; }
.card-animate:nth-child(4) { animation-delay: 0.4s; }

.stat-card {
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.stat-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover::before {
    opacity: 1;
}

.chart-card {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    transition: all 0.3s ease;
}

.chart-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
}

.trend-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
}

.trend-up {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
}

.trend-down {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

.icon-wrapper {
    position: relative;
    transition: transform 0.3s ease;
}

.stat-card:hover .icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.glass-effect {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.5);
}
</style>



<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Income Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-6 flex flex-col justify-between hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs font-semibold uppercase">Total Income</p>
                <h3 class="text-3xl font-bold text-blue-700 mt-1">$<?php echo number_format($totalIncome, 2); ?></h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500">Change</span>
            <span class="text-xs font-semibold text-green-600">+<?php echo number_format($incomeChange, 1); ?>%</span>
        </div>
    </div>

    <!-- Total Expense Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-6 flex flex-col justify-between hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs font-semibold uppercase">Total Expense</p>
                <h3 class="text-3xl font-bold text-red-700 mt-1">$<?php echo number_format($totalExpense, 2); ?></h3>
            </div>
            <div class="bg-red-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500">Change</span>
            <span class="text-xs font-semibold text-red-600"><?php echo number_format($expenseChange, 1); ?>%</span>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-6 flex flex-col justify-between hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs font-semibold uppercase">Net Balance</p>
                <h3 class="text-3xl font-bold text-indigo-700 mt-1">$<?php echo number_format($balance, 2); ?></h3>
            </div>
            <div class="bg-indigo-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500">Change</span>
            <span class="text-xs font-semibold text-green-600">+<?php echo number_format($balanceChange, 1); ?>%</span>
        </div>
    </div>

    <!-- Today's Expense Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-lg p-6 flex flex-col justify-between hover:shadow-2xl transition-all duration-300">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-gray-500 text-xs font-semibold uppercase">Today's Expense</p>
                <h3 class="text-3xl font-bold text-yellow-700 mt-1">$<?php echo number_format($todayExpense, 2); ?></h3>
            </div>
            <div class="bg-yellow-100 p-3 rounded-full">
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500">Date</span>
            <span class="text-xs font-semibold text-gray-700"><?php echo date('M d, Y'); ?></span>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Monthly Expense Chart -->
    <div class="chart-card glass-effect rounded-2xl shadow-lg p-6 card-animate opacity-0" style="animation-delay: 0.5s;">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Monthly Expenses</h2>
            <div class="bg-red-100 p-2 rounded-lg">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
        </div>
        <div class="h-80">
            <canvas id="monthlyExpenseChart"></canvas>
        </div>
    </div>

    <!-- Income vs Expense Chart -->
    <div class="chart-card glass-effect rounded-2xl shadow-lg p-6 card-animate opacity-0" style="animation-delay: 0.6s;">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Income vs Expense</h2>
            <div class="bg-blue-100 p-2 rounded-lg">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
        </div>
        <div class="h-80">
            <canvas id="comparisonChart"></canvas>
        </div>
    </div>

    <!-- Category Breakdown Chart -->
    <div class="chart-card glass-effect rounded-2xl shadow-lg p-6 card-animate opacity-0" style="animation-delay: 0.7s;">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800">Expense Categories</h2>
            <div class="bg-purple-100 p-2 rounded-lg">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
            </div>
        </div>
        <div class="h-80 flex items-center justify-center">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Chart.js global configuration
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size = 13;
Chart.defaults.color = '#6B7280';

window.monthlyExpenseData = {
    labels: <?php echo json_encode($monthlyLabels); ?>,
    data: <?php echo json_encode($monthlyValues); ?>
};
window.comparisonData = {
    labels: <?php echo json_encode($comparisonLabels); ?>,
    income: <?php echo json_encode($comparisonIncomes); ?>,
    expense: <?php echo json_encode($comparisonExpenses); ?>
};
window.categoryData = {
    labels: <?php echo json_encode($categoryLabels); ?>,
    data: <?php echo json_encode($categoryValues); ?>
};

document.addEventListener('DOMContentLoaded', function () {
    // Monthly Expense Bar Chart with Gradient
    const ctx1 = document.getElementById('monthlyExpenseChart').getContext('2d');
    const gradient1 = ctx1.createLinearGradient(0, 0, 0, 400);
    gradient1.addColorStop(0, 'rgba(239, 68, 68, 0.8)');
    gradient1.addColorStop(1, 'rgba(239, 68, 68, 0.2)');

    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: window.monthlyExpenseData.labels,
            datasets: [{
                label: 'Monthly Expenses',
                data: window.monthlyExpenseData.data,
                backgroundColor: gradient1,
                borderColor: '#ef4444',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return 'Expense: $' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });

    // Income vs Expense Area Chart
    const ctx2 = document.getElementById('comparisonChart').getContext('2d');
    const gradientIncome = ctx2.createLinearGradient(0, 0, 0, 400);
    gradientIncome.addColorStop(0, 'rgba(34, 197, 94, 0.4)');
    gradientIncome.addColorStop(1, 'rgba(34, 197, 94, 0.05)');

    const gradientExpense = ctx2.createLinearGradient(0, 0, 0, 400);
    gradientExpense.addColorStop(0, 'rgba(239, 68, 68, 0.4)');
    gradientExpense.addColorStop(1, 'rgba(239, 68, 68, 0.05)');

    new Chart(ctx2, {
        type: 'line',
        data: {
            labels: window.comparisonData.labels,
            datasets: [
                {
                    label: 'Income',
                    data: window.comparisonData.income,
                    borderColor: '#22c55e',
                    backgroundColor: gradientIncome,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#22c55e',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                },
                {
                    label: 'Expense',
                    data: window.comparisonData.expense,
                    borderColor: '#ef4444',
                    backgroundColor: gradientExpense,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#ef4444',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: { size: 13, weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });

    // Category Doughnut Chart with Custom Colors
    const ctx3 = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctx3, {
        type: 'doughnut',
        data: {
            labels: window.categoryData.labels,
            datasets: [{
                data: window.categoryData.data,
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(168, 85, 247, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(234, 179, 8, 0.8)',
                    'rgba(20, 184, 166, 0.8)'
                ],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        font: { size: 12, weight: '600' },
                        usePointStyle: true,
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return {
                                        text: `${label}: ${percentage}%`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 12,
                    borderRadius: 8,
                    titleFont: { size: 14, weight: 'bold' },
                    bodyFont: { size: 13 },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: $${value.toFixed(2)} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
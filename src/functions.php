<?php
require_once __DIR__ . '/../config/database.php';

// Income Functions
function addIncome($title, $amount, $date, $user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO incomes (title, amount, date, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdsi", $title, $amount, $date, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}
function getExpenseCategoryBreakdown($user_id) {
    $conn = getDBConnection();
    $query = "
        SELECT c.name AS category, SUM(e.amount) AS total
        FROM expenses e
        JOIN categories c ON e.category_id = c.id
        WHERE e.user_id = ?
        GROUP BY c.id
        ORDER BY total DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $breakdown = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $breakdown;
}
function getAllIncomes($user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM incomes WHERE user_id = ? ORDER BY date DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $incomes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $incomes;
}

function deleteIncome($id, $user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM incomes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

function getTotalIncome($user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM incomes WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $row['total'] ?? 0;
}

// Expense Functions
function addExpense($category_id, $amount, $date, $user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO expenses (category_id, amount, date, user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("idsi", $category_id, $amount, $date, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

function getAllExpenses($user_id) {
    $conn = getDBConnection();
    $query = "SELECT e.*, c.name as category_name FROM expenses e 
              LEFT JOIN categories c ON e.category_id = c.id 
              WHERE e.user_id = ?
              ORDER BY e.date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $expenses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $expenses;
}

function deleteExpense($id, $user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

function getTotalExpense($user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM expenses WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $row['total'] ?? 0;
}

function getTodayExpense($user_id) {
    $conn = getDBConnection();
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT SUM(amount) as total FROM expenses WHERE date = ? AND user_id = ?");
    $stmt->bind_param("si", $today, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $row['total'] ?? 0;
}

// Category Functions
function addCategory($name, $user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("INSERT INTO categories (name, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $name, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

function getAllCategories($user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY name ASC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return $categories;
}

function deleteCategory($id, $user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

function updateCategory($id, $name, $user_id) {
    $conn = getDBConnection();
    $stmt = $conn->prepare("UPDATE categories SET name = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("sii", $name, $id, $user_id);
    $result = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $result;
}

// Chart Data Functions
function getMonthlyExpenseData($user_id) {
    $conn = getDBConnection();
    $query = "SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as total 
              FROM expenses 
              WHERE user_id = ?
              GROUP BY DATE_FORMAT(date, '%Y-%m') 
              ORDER BY month DESC 
              LIMIT 6";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $conn->close();
    return array_reverse($data);
}

function getIncomeVsExpenseData($user_id) {
    $conn = getDBConnection();
    
    // Get last 6 months incomes
    $query = "SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as total 
              FROM incomes 
              WHERE user_id = ?
              GROUP BY DATE_FORMAT(date, '%Y-%m') 
              ORDER BY month DESC 
              LIMIT 6";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $incomes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Get last 6 months expenses
    $query = "SELECT DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as total 
              FROM expenses 
              WHERE user_id = ?
              GROUP BY DATE_FORMAT(date, '%Y-%m') 
              ORDER BY month DESC 
              LIMIT 6";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $expenses = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    $conn->close();
    
    return [
        'incomes' => array_reverse($incomes),
        'expenses' => array_reverse($expenses)
    ];
}
?>

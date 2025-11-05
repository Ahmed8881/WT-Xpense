<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/functions.php';

checkAuth();

$user_id = $_SESSION['user_id'];
$message = '';
$messageType = '';
$editCategory = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $name = trim($_POST['name'] ?? '');
        
        if (!empty($name)) {
            if (addCategory($name, $user_id)) {
                $message = 'Category added successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to add category.';
                $messageType = 'error';
            }
        } else {
            $message = 'Category name cannot be empty.';
            $messageType = 'error';
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'update') {
        $id = $_POST['id'] ?? 0;
        $name = trim($_POST['name'] ?? '');
        
        if (!empty($name) && $id > 0) {
            if (updateCategory($id, $name, $user_id)) {
                $message = 'Category updated successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to update category.';
                $messageType = 'error';
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $id = $_POST['id'] ?? 0;
        if (deleteCategory($id, $user_id)) {
            $message = 'Category deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Failed to delete category. It may be in use.';
            $messageType = 'error';
        }
    }
}

// Check if editing
if (isset($_GET['edit'])) {
    $categories = getAllCategories($user_id);
    foreach ($categories as $cat) {
        if ($cat['id'] == $_GET['edit']) {
            $editCategory = $cat;
            break;
        }
    }
}

// Get all categories
$categories = getAllCategories($user_id);

$pageTitle = 'Category Management';
include 'includes/header.php';
?>

<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-green-50 py-10">
    <div class="max-w-6xl mx-auto px-4">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Category Management</h2>
                <p class="text-gray-600 mt-2">Manage your expense categories for better tracking</p>
            </div>
            <div>
                <a href="logout.php" class="inline-block bg-gray-800 text-white px-5 py-2 rounded-lg font-semibold shadow hover:bg-gray-900 transition">Logout</a>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg shadow <?php echo $messageType === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Add/Edit Category Form -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-blue-100 via-gray-100 to-green-100 rounded-2xl shadow-lg p-8 border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">
                        <?php echo $editCategory ? 'Edit Category' : 'Add New Category'; ?>
                    </h3>
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="<?php echo $editCategory ? 'update' : 'add'; ?>">
                        <?php if ($editCategory): ?>
                            <input type="hidden" name="id" value="<?php echo $editCategory['id']; ?>">
                        <?php endif; ?>
                        
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Category Name</label>
                            <input 
                                type="text" 
                                name="name" 
                                required
                                value="<?php echo $editCategory ? htmlspecialchars($editCategory['name']) : ''; ?>"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-transparent outline-none transition-all"
                                placeholder="e.g., Food, Transport"
                            >
                        </div>
                        
                        <button 
                            type="submit" 
                            class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition shadow-lg"
                        >
                            <?php echo $editCategory ? 'Update Category' : 'Add Category'; ?>
                        </button>
                        
                        <?php if ($editCategory): ?>
                            <a 
                                href="categories.php" 
                                class="block w-full text-center bg-gray-300 text-gray-700 py-3 rounded-xl font-semibold hover:bg-gray-400 transition mt-2"
                            >
                                Cancel
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Categories List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">All Categories</h3>
                    
                    <?php if (empty($categories)): ?>
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <p class="text-gray-500">No categories yet. Add your first category!</p>
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php foreach ($categories as $category): ?>
                                <div class="border border-gray-200 rounded-xl p-6 bg-gradient-to-r from-blue-50 to-green-50 hover:shadow-xl transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                            </div>
                                            <span class="font-semibold text-gray-800">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <a 
                                                href="categories.php?edit=<?php echo $category['id']; ?>" 
                                                class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                                            >
                                                Edit
                                            </a>
                                            <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
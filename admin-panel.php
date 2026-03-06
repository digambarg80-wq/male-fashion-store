<?php
// Start session for admin login
session_start();

require_once 'includes/db_connect.php';

// Simple admin login (for college project)
$admin_username = 'admin';
$admin_password = 'admin123';

// Handle login
if(isset($_POST['login'])) {
    if($_POST['username'] == $admin_username && $_POST['password'] == $admin_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Invalid username or password!";
    }
}

// Handle logout
if(isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin-panel.php');
    exit;
}

// Handle add product
if(isset($_POST['add_product']) && isset($_SESSION['admin_logged_in'])) {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $sale_price = $_POST['sale_price'] ?: null;
    $stock = $_POST['stock'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    // Create slug from name
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    $stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, description, price, sale_price, stock, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if($stmt->execute([$name, $slug, $category_id, $description, $price, $sale_price, $stock, $featured])) {
        $success = "Product added successfully!";
    } else {
        $error = "Error adding product!";
    }
}

// Handle delete product
if(isset($_GET['delete']) && isset($_SESSION['admin_logged_in'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $success = "Product deleted successfully!";
}

// Handle update product
if(isset($_POST['update_product']) && isset($_SESSION['admin_logged_in'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $sale_price = $_POST['sale_price'] ?: null;
    $stock = $_POST['stock'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    $stmt = $pdo->prepare("UPDATE products SET name=?, category_id=?, description=?, price=?, sale_price=?, stock=?, featured=? WHERE id=?");
    
    if($stmt->execute([$name, $category_id, $description, $price, $sale_price, $stock, $featured, $id])) {
        $success = "Product updated successfully!";
    } else {
        $error = "Error updating product!";
    }
}

// Get all products for display
$products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();

// Get all categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Check if editing
$edit_product = null;
if(isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_product = $stmt->fetch();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Men's Fashion Store</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .login-form {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            background: #f5f5f5;
            border-radius: 8px;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-primary {
            background: #000;
            color: #fff;
        }
        .btn-danger {
            background: #dc3545;
            color: #fff;
        }
        .btn-success {
            background: #28a745;
            color: #fff;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }
        .table th, .table td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .table th {
            background: #f5f5f5;
        }
        .table tr:hover {
            background: #f9f9f9;
        }
        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .action-buttons a {
            margin-right: 0.5rem;
            text-decoration: none;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #f5f5f5;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }
        .stat-card h3 {
            margin: 0;
            font-size: 2rem;
            color: #000;
        }
        .stat-card p {
            margin: 0.5rem 0 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Admin Panel - Men's Fashion Store</h1>
            <?php if(isset($_SESSION['admin_logged_in'])): ?>
                <a href="?logout=1" class="btn btn-danger">Logout</a>
            <?php endif; ?>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if(!isset($_SESSION['admin_logged_in'])): ?>
            <!-- Login Form -->
            <div class="login-form">
                <h2>Admin Login</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password:</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                </form>
                <p style="margin-top: 1rem; color: #666;">Default: admin / admin123</p>
            </div>
        <?php else: ?>
            <!-- Dashboard Stats -->
            <?php
            $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
            $total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
            $total_stock = $pdo->query("SELECT SUM(stock) FROM products")->fetchColumn();
            $featured_count = $pdo->query("SELECT COUNT(*) FROM products WHERE featured = 1")->fetchColumn();
            ?>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $total_products; ?></h3>
                    <p>Total Products</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $total_categories; ?></h3>
                    <p>Categories</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $total_stock; ?></h3>
                    <p>Items in Stock</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $featured_count; ?></h3>
                    <p>Featured Products</p>
                </div>
            </div>

            <!-- Add/Edit Product Form -->
            <div style="background: #f5f5f5; padding: 2rem; border-radius: 8px; margin-bottom: 2rem;">
                <h2><?php echo $edit_product ? 'Edit Product' : 'Add New Product'; ?></h2>
                <form method="POST">
                    <?php if($edit_product): ?>
                        <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label>Product Name:</label>
                        <input type="text" name="name" value="<?php echo $edit_product['name'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category:</label>
                        <select name="category_id" required>
                            <?php foreach($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo ($edit_product && $edit_product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo $category['name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="description" rows="4" required><?php echo $edit_product['description'] ?? ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Price (₹):</label>
                        <input type="number" name="price" step="0.01" value="<?php echo $edit_product['price'] ?? ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Sale Price (₹) - Leave empty if no sale:</label>
                        <input type="number" name="sale_price" step="0.01" value="<?php echo $edit_product['sale_price'] ?? ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Stock:</label>
                        <input type="number" name="stock" value="<?php echo $edit_product['stock'] ?? '10'; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="featured" <?php echo ($edit_product && $edit_product['featured']) ? 'checked' : ''; ?>>
                            Featured Product
                        </label>
                    </div>
                    
                    <button type="submit" name="<?php echo $edit_product ? 'update_product' : 'add_product'; ?>" class="btn btn-primary">
                        <?php echo $edit_product ? 'Update Product' : 'Add Product'; ?>
                    </button>
                    
                    <?php if($edit_product): ?>
                        <a href="admin-panel.php" class="btn" style="margin-left: 1rem;">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Products Table -->
            <h2>Manage Products</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Sale Price</th>
                        <th>Stock</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo $product['name']; ?></td>
                        <td><?php echo $product['category_name']; ?></td>
                        <td>₹<?php echo number_format($product['price']); ?></td>
                        <td>
                            <?php if($product['sale_price']): ?>
                                ₹<?php echo number_format($product['sale_price']); ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo $product['stock']; ?></td>
                        <td><?php echo $product['featured'] ? 'Yes' : 'No'; ?></td>
                        <td class="action-buttons">
                            <a href="?edit=<?php echo $product['id']; ?>" class="btn btn-primary">Edit</a>
                            <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
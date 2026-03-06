<?php
require_once '../includes/auth.php';

// Redirect if not admin
if(!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/db.php';

// Handle product operations
if(isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $sale_price = $_POST['sale_price'] ?: null;
    $stock = $_POST['stock'];
    $featured = isset($_POST['featured']) ? 1 : 0;
    
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    $stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, description, price, sale_price, stock, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $slug, $category_id, $description, $price, $sale_price, $stock, $featured]);
    $message = "Product added successfully!";
}

if(isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    $message = "Product deleted successfully!";
}

// Get statistics
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'customer'")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$recent_users = $pdo->query("SELECT * FROM users WHERE user_type = 'customer' ORDER BY created_at DESC LIMIT 5")->fetchAll();

include '../includes/header.php';
?>

<div class="admin-dashboard" style="max-width: 1400px; margin: 2rem auto; padding: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Admin Dashboard</h1>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>

    <?php if(isset($message)): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 1.5rem; border-radius: 8px;">
            <h3 style="margin: 0; font-size: 2rem;"><?php echo $total_users; ?></h3>
            <p style="margin: 0;">Total Customers</p>
        </div>
        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; padding: 1.5rem; border-radius: 8px;">
            <h3 style="margin: 0; font-size: 2rem;"><?php echo $total_products; ?></h3>
            <p style="margin: 0;">Total Products</p>
        </div>
        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; padding: 1.5rem; border-radius: 8px;">
            <h3 style="margin: 0; font-size: 2rem;">0</h3>
            <p style="margin: 0;">Total Orders</p>
        </div>
        <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; padding: 1.5rem; border-radius: 8px;">
            <h3 style="margin: 0; font-size: 2rem;">₹0</h3>
            <p style="margin: 0;">Revenue</p>
        </div>
    </div>

    <!-- Admin Navigation -->
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid #f0f0f0; padding-bottom: 1rem;">
        <button onclick="showAdminTab('products')" id="productsTabBtn" class="btn btn-primary">Products</button>
        <button onclick="showAdminTab('users')" id="usersTabBtn" class="btn">Users</button>
        <button onclick="showAdminTab('add')" id="addTabBtn" class="btn">Add Product</button>
    </div>

    <!-- Products Tab -->
    <div id="adminProductsTab">
        <h2>Manage Products</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 1rem; text-align: left;">ID</th>
                    <th style="padding: 1rem; text-align: left;">Name</th>
                    <th style="padding: 1rem; text-align: left;">Category</th>
                    <th style="padding: 1rem; text-align: left;">Price</th>
                    <th style="padding: 1rem; text-align: left;">Stock</th>
                    <th style="padding: 1rem; text-align: left;">Featured</th>
                    <th style="padding: 1rem; text-align: left;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $products = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();
                foreach($products as $product):
                ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 1rem;"><?php echo $product['id']; ?></td>
                    <td style="padding: 1rem;"><?php echo $product['name']; ?></td>
                    <td style="padding: 1rem;"><?php echo $product['category_name']; ?></td>
                    <td style="padding: 1rem;">₹<?php echo number_format($product['price']); ?></td>
                    <td style="padding: 1rem;"><?php echo $product['stock']; ?></td>
                    <td style="padding: 1rem;"><?php echo $product['featured'] ? 'Yes' : 'No'; ?></td>
                    <td style="padding: 1rem;">
                        <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Users Tab -->
    <div id="adminUsersTab" style="display: none;">
        <h2>Registered Users</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 1rem; text-align: left;">ID</th>
                    <th style="padding: 1rem; text-align: left;">Username</th>
                    <th style="padding: 1rem; text-align: left;">Full Name</th>
                    <th style="padding: 1rem; text-align: left;">Email</th>
                    <th style="padding: 1rem; text-align: left;">Joined</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $users = $pdo->query("SELECT * FROM users WHERE user_type = 'customer' ORDER BY created_at DESC")->fetchAll();
                foreach($users as $user):
                ?>
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 1rem;"><?php echo $user['id']; ?></td>
                    <td style="padding: 1rem;"><?php echo $user['username']; ?></td>
                    <td style="padding: 1rem;"><?php echo $user['full_name']; ?></td>
                    <td style="padding: 1rem;"><?php echo $user['email']; ?></td>
                    <td style="padding: 1rem;"><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add Product Tab -->
    <div id="adminAddTab" style="display: none;">
        <h2>Add New Product</h2>
        <form method="POST" style="max-width: 600px;">
            <div class="form-group">
                <label>Product Name:</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Category:</label>
                <select name="category_id" required>
                    <?php
                    $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
                    foreach($categories as $category):
                    ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Price (₹):</label>
                <input type="number" name="price" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label>Sale Price (₹):</label>
                <input type="number" name="sale_price" step="0.01">
            </div>
            
            <div class="form-group">
                <label>Stock:</label>
                <input type="number" name="stock" value="10" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="featured">
                    Featured Product
                </label>
            </div>
            
            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
        </form>
    </div>
</div>

<script>
function showAdminTab(tab) {
    document.getElementById('adminProductsTab').style.display = 'none';
    document.getElementById('adminUsersTab').style.display = 'none';
    document.getElementById('adminAddTab').style.display = 'none';
    
    document.getElementById('productsTabBtn').className = 'btn';
    document.getElementById('usersTabBtn').className = 'btn';
    document.getElementById('addTabBtn').className = 'btn';
    
    if(tab === 'products') {
        document.getElementById('adminProductsTab').style.display = 'block';
        document.getElementById('productsTabBtn').className = 'btn btn-primary';
    }
    if(tab === 'users') {
        document.getElementById('adminUsersTab').style.display = 'block';
        document.getElementById('usersTabBtn').className = 'btn btn-primary';
    }
    if(tab === 'add') {
        document.getElementById('adminAddTab').style.display = 'block';
        document.getElementById('addTabBtn').className = 'btn btn-primary';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
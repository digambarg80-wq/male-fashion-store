<?php
require_once 'includes/db_connect.php';

// Simple admin authentication (in real app, use proper authentication)
$password = 'admin123'; // Change this!
$authenticated = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && $_POST['password'] === $password) {
    $authenticated = true;
}

// Handle product addition
if ($authenticated && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $sale_price = $_POST['sale_price'] ?: null;
    $stock = $_POST['stock'];
    
    $stmt = $pdo->prepare("INSERT INTO products (name, slug, category_id, description, price, sale_price, stock) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $slug, $category_id, $description, $price, $sale_price, $stock]);
    
    $message = "Product added successfully!";
}

// Get categories for dropdown
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div style="max-width: 800px; margin: 2rem auto; padding: 2rem;">
        <h1>Admin Panel</h1>
        
        <?php if(!$authenticated): ?>
        <form method="POST" style="max-width: 300px;">
            <div style="margin-bottom: 1rem;">
                <label>Password:</label>
                <input type="password" name="password" required style="width: 100%; padding: 0.5rem;">
            </div>
            <button type="submit" style="padding: 0.5rem 1rem;">Login</button>
        </form>
        <?php else: ?>
        
        <?php if(isset($message)): ?>
        <div style="background: #4CAF50; color: white; padding: 1rem; margin-bottom: 1rem;">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <h2>Add New Product</h2>
        <form method="POST" style="display: grid; gap: 1rem;">
            <input type="hidden" name="add_product" value="1">
            
            <div>
                <label>Product Name:</label>
                <input type="text" name="name" required style="width: 100%; padding: 0.5rem;">
            </div>
            
            <div>
                <label>Category:</label>
                <select name="category_id" required style="width: 100%; padding: 0.5rem;">
                    <?php foreach($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label>Description:</label>
                <textarea name="description" required style="width: 100%; padding: 0.5rem;" rows="4"></textarea>
            </div>
            
            <div>
                <label>Price (₹):</label>
                <input type="number" name="price" required step="0.01" style="width: 100%; padding: 0.5rem;">
            </div>
            
            <div>
                <label>Sale Price (₹) - Leave empty if no sale:</label>
                <input type="number" name="sale_price" step="0.01" style="width: 100%; padding: 0.5rem;">
            </div>
            
            <div>
                <label>Stock:</label>
                <input type="number" name="stock" value="10" required style="width: 100%; padding: 0.5rem;">
            </div>
            
            <button type="submit" style="padding: 1rem; background: black; color: white; border: none; cursor: pointer;">Add Product</button>
        </form>
        
        <h2 style="margin-top: 3rem;">Existing Products</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; padding: 0.5rem; border-bottom: 1px solid #ddd;">ID</th>
                    <th style="text-align: left; padding: 0.5rem; border-bottom: 1px solid #ddd;">Name</th>
                    <th style="text-align: left; padding: 0.5rem; border-bottom: 1px solid #ddd;">Price</th>
                    <th style="text-align: left; padding: 0.5rem; border-bottom: 1px solid #ddd;">Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $products = $pdo->query("SELECT id, name, price, stock FROM products ORDER BY id DESC LIMIT 10")->fetchAll();
                foreach($products as $product):
                ?>
                <tr>
                    <td style="padding: 0.5rem; border-bottom: 1px solid #ddd;"><?php echo $product['id']; ?></td>
                    <td style="padding: 0.5rem; border-bottom: 1px solid #ddd;"><?php echo $product['name']; ?></td>
                    <td style="padding: 0.5rem; border-bottom: 1px solid #ddd;">₹<?php echo number_format($product['price']); ?></td>
                    <td style="padding: 0.5rem; border-bottom: 1px solid #ddd;"><?php echo $product['stock']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php endif; ?>
    </div>
</body>
</html>
<?php
require_once '../includes/auth.php';

// Redirect if not admin
if(!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/db.php';

// Get statistics for dashboard
$total_customers = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'customer'")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Calculate total revenue
$revenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE payment_status = 'paid' OR status != 'cancelled'")->fetchColumn();
$revenue = $revenue ?: 0;

// Get monthly sales for chart
$monthly_sales = $pdo->query("
    SELECT DATE_FORMAT(order_date, '%M') as month, 
           COUNT(*) as order_count, 
           SUM(total_amount) as total 
    FROM orders 
    WHERE order_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY MONTH(order_date)
    ORDER BY order_date DESC
    LIMIT 6
")->fetchAll();

// Get top selling products
$top_products = $pdo->query("
    SELECT p.name, SUM(oi.quantity) as total_sold, SUM(oi.quantity * oi.price) as revenue
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    GROUP BY oi.product_id
    ORDER BY total_sold DESC
    LIMIT 5
")->fetchAll();

// Get recent orders
$recent_orders = $pdo->query("
    SELECT o.*, u.full_name 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
    LIMIT 5
")->fetchAll();

include '../includes/header.php';
?>

<style>
.dashboard-container {
    max-width: 1400px;
    margin: 2rem auto;
    padding: 2rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

.stat-info h3 {
    margin: 0;
    font-size: 0.9rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.stat-info p {
    margin: 0.5rem 0 0;
    font-size: 2rem;
    font-weight: bold;
    color: #333;
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon .material-icons {
    font-size: 30px;
    color: white;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.dashboard-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
}

.card-header h2 {
    margin: 0;
    font-size: 1.2rem;
    color: #333;
}

.card-header .material-icons {
    color: #667eea;
}

.product-list, .order-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.product-item, .order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.product-item:last-child, .order-item:last-child {
    border-bottom: none;
}

.product-info h4 {
    margin: 0;
    font-size: 1rem;
    color: #333;
}

.product-info p {
    margin: 0.25rem 0 0;
    font-size: 0.85rem;
    color: #666;
}

.product-stats {
    text-align: right;
}

.product-stats .sold {
    font-weight: bold;
    color: #28a745;
}

.product-stats .revenue {
    font-size: 0.85rem;
    color: #667eea;
}

.order-info {
    flex: 1;
}

.order-info .customer {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.order-info .order-meta {
    font-size: 0.85rem;
    color: #666;
}

.order-status {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #cce5ff; color: #004085; }
.status-shipped { background: #d4edda; color: #155724; }
.status-delivered { background: #d4edda; color: #155724; }

.view-all {
    text-align: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #f0f0f0;
}

.view-all a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.btn-outline {
    background: transparent;
    border: 2px solid #667eea;
    color: #667eea;
}

.btn-outline:hover {
    background: #667eea;
    color: white;
}

.chart-container {
    height: 300px;
    margin-top: 1rem;
}
</style>

<div class="dashboard-container">
    <h1>Admin Dashboard</h1>
    
    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Customers</h3>
                <p><?php echo $total_customers; ?></p>
            </div>
            <div class="stat-icon">
                <span class="material-icons">people</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Products</h3>
                <p><?php echo $total_products; ?></p>
            </div>
            <div class="stat-icon">
                <span class="material-icons">inventory</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Orders</h3>
                <p><?php echo $total_orders; ?></p>
            </div>
            <div class="stat-icon">
                <span class="material-icons">shopping_bag</span>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-info">
                <h3>Total Revenue</h3>
                <p>₹<?php echo number_format($revenue); ?></p>
            </div>
            <div class="stat-icon">
                <span class="material-icons">payments</span>
            </div>
        </div>
    </div>
    
    <!-- Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Top Products Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Top Selling Products</h2>
                <span class="material-icons">trending_up</span>
            </div>
            
            <?php if(empty($top_products)): ?>
                <p style="text-align: center; color: #999; padding: 2rem;">No sales data yet</p>
            <?php else: ?>
                <ul class="product-list">
                    <?php foreach($top_products as $product): ?>
                    <li class="product-item">
                        <div class="product-info">
                            <h4><?php echo $product['name']; ?></h4>
                            <p>Sold: <?php echo $product['total_sold']; ?> units</p>
                        </div>
                        <div class="product-stats">
                            <div class="sold">₹<?php echo number_format($product['revenue']); ?></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        
        <!-- Recent Orders Card -->
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Recent Orders</h2>
                <span class="material-icons">schedule</span>
            </div>
            
            <?php if(empty($recent_orders)): ?>
                <p style="text-align: center; color: #999; padding: 2rem;">No orders yet</p>
            <?php else: ?>
                <ul class="order-list">
                    <?php foreach($recent_orders as $order): ?>
                    <li class="order-item">
                        <div class="order-info">
                            <div class="customer"><?php echo $order['full_name']; ?></div>
                            <div class="order-meta">
                                #<?php echo $order['order_number']; ?> • 
                                ₹<?php echo number_format($order['total_amount']); ?>
                            </div>
                        </div>
                        <span class="order-status status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="view-all">
                    <a href="orders.php">View All Orders →</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="dashboard-card">
        <div class="card-header">
            <h2>Quick Actions</h2>
            <span class="material-icons">bolt</span>
        </div>
        
        <div class="action-buttons">
            <a href="products.php?action=add" class="btn btn-primary">
                <span class="material-icons">add</span>
                Add New Product
            </a>
            <a href="products.php" class="btn btn-outline">
                <span class="material-icons">inventory</span>
                Manage Products
            </a>
            <a href="users.php" class="btn btn-outline">
                <span class="material-icons">people</span>
                View Users
            </a>
            <a href="orders.php" class="btn btn-outline">
                <span class="material-icons">receipt</span>
                View Orders
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
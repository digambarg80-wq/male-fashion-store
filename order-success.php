<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$order_number = $_GET['order'] ?? '';

if(!$order_number || !isLoggedIn()) {
    header('Location: /male-fashion-store/index.php');
    exit;
}

// Get order details
$stmt = $pdo->prepare("SELECT o.*, u.full_name, u.email 
                       FROM orders o 
                       JOIN users u ON o.user_id = u.id 
                       WHERE o.order_number = ? AND o.user_id = ?");
$stmt->execute([$order_number, $_SESSION['user_id']]);
$order = $stmt->fetch();

if(!$order) {
    header('Location: /male-fashion-store/index.php');
    exit;
}

// Get order items
$stmt = $pdo->prepare("SELECT oi.*, p.name 
                       FROM order_items oi 
                       JOIN products p ON oi.product_id = p.id 
                       WHERE oi.order_id = ?");
$stmt->execute([$order['id']]);
$items = $stmt->fetchAll();

include 'includes/header.php';
?>

<style>
.success-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    text-align: center;
}

.success-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 2rem;
}

.success-icon .material-icons {
    font-size: 60px;
    color: white;
}

.order-card {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    text-align: left;
    margin-top: 2rem;
}

.order-header {
    display: flex;
    justify-content: space-between;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f0f0;
    margin-bottom: 1rem;
}

.order-number {
    font-size: 1.2rem;
    font-weight: bold;
    color: #667eea;
}

.order-status {
    padding: 0.25rem 1rem;
    background: #fff3cd;
    color: #856404;
    border-radius: 20px;
    font-size: 0.9rem;
}

.order-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f0f0f0;
}

.order-total {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #333;
    font-size: 1.2rem;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
}

.invoice-btn {
    display: inline-block;
    padding: 1rem 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    margin-top: 2rem;
    transition: transform 0.3s;
}

.invoice-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}
</style>

<div class="success-container">
    <div class="success-icon">
        <span class="material-icons">check</span>
    </div>
    
    <h1>Order Placed Successfully!</h1>
    <p>Thank you for your purchase. Your order has been confirmed.</p>
    
    <div class="order-card">
        <div class="order-header">
            <span class="order-number">Order #<?php echo $order['order_number']; ?></span>
            <span class="order-status"><?php echo ucfirst($order['order_status']); ?></span>
        </div>
        
        <?php foreach($items as $item): ?>
        <div class="order-item">
            <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
            <span>₹<?php echo number_format($item['price'] * $item['quantity']); ?></span>
        </div>
        <?php endforeach; ?>
        
        <div class="order-total">
            <span>Total Paid</span>
            <span>₹<?php echo number_format($order['total_amount']); ?></span>
        </div>
        
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f0f0f0;">
            <p><strong>Payment Status:</strong> <?php echo ucfirst($order['payment_status']); ?></p>
            <p><strong>Shipping Address:</strong> <?php echo $order['shipping_address']; ?></p>
        </div>
    </div>
    
    <a href="/male-fashion-store/invoice.php?order=<?php echo $order['order_number']; ?>" class="invoice-btn">
        <span class="material-icons" style="vertical-align: middle; margin-right: 0.5rem;">download</span>
        Download Invoice
    </a>
    
    <p style="margin-top: 2rem;">
        <a href="/male-fashion-store/products.php">Continue Shopping</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>
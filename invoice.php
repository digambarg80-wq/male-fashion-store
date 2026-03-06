<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if(!isLoggedIn()) {
    header('Location: /male-fashion-store/login.php');
    exit;
}

$order_number = $_GET['order'] ?? $_SESSION['last_order'] ?? '';

if(!$order_number) {
    header('Location: /male-fashion-store/index.php');
    exit;
}

// Get order details
$stmt = $pdo->prepare("SELECT o.*, u.full_name, u.email, u.phone 
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
.invoice-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-radius: 12px;
}

.invoice-header {
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 2px solid #f0f0f0;
}

.invoice-header h1 {
    color: #667eea;
    margin-bottom: 0.5rem;
}

.invoice-header h2 {
    color: #28a745;
    font-size: 1.2rem;
    font-weight: normal;
}

.invoice-info {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.info-item label {
    font-weight: bold;
    color: #666;
    display: block;
    margin-bottom: 0.25rem;
}

.info-item p {
    margin: 0;
    font-size: 1.1rem;
}

.invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2rem;
}

.invoice-table th {
    background: #667eea;
    color: white;
    padding: 0.75rem;
    text-align: left;
}

.invoice-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #f0f0f0;
}

.invoice-table tfoot td {
    border-bottom: none;
    padding-top: 1rem;
    font-weight: bold;
}

.total-row {
    font-size: 1.2rem;
    background: #f8f9fa;
}

.payment-note {
    background: #e8f5e9;
    padding: 1rem;
    border-radius: 8px;
    color: #2e7d32;
    text-align: center;
    margin-bottom: 2rem;
}

.invoice-footer {
    text-align: center;
    color: #999;
    font-size: 0.9rem;
    padding-top: 2rem;
    border-top: 1px solid #f0f0f0;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin: 2rem 0;
}

.btn {
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

@media print {
    .action-buttons, .footer, header, .mobile-menu-btn {
        display: none !important;
    }
    .invoice-container {
        box-shadow: none;
        padding: 0;
    }
}
</style>

<div class="invoice-container" id="invoice">
    <div class="invoice-header">
        <h1>MENS FASHION STORE</h1>
        <h2>Order Invoice</h2>
    </div>
    
    <div class="payment-note">
        <span class="material-icons" style="vertical-align: middle;">check_circle</span>
        Payment Method: Cash on Delivery
    </div>
    
    <div class="invoice-info">
        <div class="info-item">
            <label>Invoice Number:</label>
            <p><?php echo $order['order_number']; ?></p>
        </div>
        <div class="info-item">
            <label>Order Date:</label>
            <p><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></p>
        </div>
        <div class="info-item">
            <label>Customer Name:</label>
            <p><?php echo $order['full_name']; ?></p>
        </div>
        <div class="info-item">
            <label>Order Status:</label>
            <p><span style="color: #f39c12; font-weight: bold;"><?php echo ucfirst($order['status']); ?></span></p>
        </div>
    </div>
    
    <table class="invoice-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item): ?>
            <tr>
                <td><?php echo $item['name']; ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>₹<?php echo number_format($item['price']); ?></td>
                <td>₹<?php echo number_format($item['price'] * $item['quantity']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Total Amount:</td>
                <td>₹<?php echo number_format($order['total_amount']); ?></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="info-item" style="margin-bottom: 2rem;">
        <label>Delivery Address:</label>
        <p><?php echo $_POST['address'] ?? 'Address provided at checkout'; ?></p>
    </div>
    
    <div class="action-buttons">
        <button onclick="window.print()" class="btn btn-primary">
            <span class="material-icons">print</span>
            Print Invoice
        </button>
        <a href="/male-fashion-store/products.php" class="btn btn-secondary">
            <span class="material-icons">shopping_bag</span>
            Continue Shopping
        </a>
    </div>
    
    <div class="invoice-footer">
        <p>Thank you for shopping with us!</p>
        <p>This is a computer generated invoice - no signature required.</p>
        <p>Pay ₹<?php echo number_format($order['total_amount']); ?> at the time of delivery.</p>
    </div>
</div>

<script>
// Auto-print on page load? Remove comment if you want auto-print
// window.onload = function() { window.print(); }
</script>

<?php include 'includes/footer.php'; ?>
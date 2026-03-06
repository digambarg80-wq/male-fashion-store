<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if(!isLoggedIn()) {
    header('Location: /male-fashion-store/login.php');
    exit;
}

// Get cart items
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.sale_price 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

if(empty($cart_items)) {
    header('Location: /male-fashion-store/cart.php');
    exit;
}

// Calculate total
$total = 0;
foreach($cart_items as $item) {
    $price = $item['sale_price'] ?: $item['price'];
    $total += $price * $item['quantity'];
}

// Process order
$success = '';
$error = '';

if(isset($_POST['place_order'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    if(empty($name) || empty($phone) || empty($address)) {
        $error = "Please fill all fields";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Generate order number
            $order_number = 'ORD' . time() . rand(100, 999);
            
            // Insert order
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, total_amount, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $order_number, $total]);
            $order_id = $pdo->lastInsertId();
            
            // Insert order items
            foreach($cart_items as $item) {
                $price = $item['sale_price'] ?: $item['price'];
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $price]);
            }
            
            // Clear cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            
            $pdo->commit();
            
            // Save order number for invoice
            $_SESSION['last_order'] = $order_number;
            
            // Redirect to invoice
            header('Location: /male-fashion-store/invoice.php?order=' . $order_number);
            exit;
            
        } catch(Exception $e) {
            $pdo->rollBack();
            $error = "Order failed: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<style>
.checkout-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
}

.checkout-box {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #333;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    font-size: 1rem;
}

.form-group input:focus,
.form-group textarea:focus {
    border-color: #667eea;
    outline: none;
}

.order-summary {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.order-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e0e0e0;
}

.order-total {
    font-size: 1.2rem;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #333;
}

.place-order-btn {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s;
}

.place-order-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.alert-error {
    background: #fee;
    color: #c33;
    border: 1px solid #fcc;
}

.payment-method {
    padding: 1rem;
    background: #e8f5e9;
    border: 2px solid #4caf50;
    border-radius: 8px;
    color: #2e7d32;
    font-weight: 500;
    text-align: center;
    margin-bottom: 1.5rem;
}
</style>

<div class="checkout-container">
    <h1>Checkout</h1>
    
    <?php if($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div class="checkout-box">
        <!-- Payment Method Notice -->
        <div class="payment-method">
            <span class="material-icons" style="vertical-align: middle;">info</span>
            Payment Method: Cash on Delivery (Pay when you receive)
        </div>
        
        <!-- Order Summary -->
        <div class="order-summary">
            <h3>Order Summary</h3>
            <?php foreach($cart_items as $item): ?>
            <div class="order-item">
                <span><?php echo $item['name']; ?> x <?php echo $item['quantity']; ?></span>
                <span>₹<?php echo number_format(($item['sale_price'] ?: $item['price']) * $item['quantity']); ?></span>
            </div>
            <?php endforeach; ?>
            <div class="order-total">
                <span>Total Amount</span>
                <span>₹<?php echo number_format($total); ?></span>
            </div>
        </div>
        
        <!-- Delivery Form -->
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?php echo $_SESSION['full_name'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" required>
            </div>
            
            <div class="form-group">
                <label>Delivery Address</label>
                <textarea name="address" rows="4" required></textarea>
            </div>
            
            <button type="submit" name="place_order" class="place-order-btn">
                Confirm Order • Pay ₹<?php echo number_format($total); ?> on Delivery
            </button>
        </form>
        
        <p style="text-align: center; margin-top: 1rem; color: #666;">
            <small>No advance payment required. Pay only when you receive your order.</small>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
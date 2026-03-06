<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

header('Content-Type: application/json');

if(!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Get cart items
$stmt = $pdo->prepare("SELECT c.*, p.price, p.sale_price 
                       FROM cart c 
                       JOIN products p ON c.product_id = p.id 
                       WHERE c.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$cart_items = $stmt->fetchAll();

if(empty($cart_items)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Calculate total
$total = 0;
foreach($cart_items as $item) {
    $price = $item['sale_price'] ?: $item['price'];
    $total += $price * $item['quantity'];
}

// Generate order number
$order_number = 'ORD' . time() . rand(100, 999);

try {
    $pdo->beginTransaction();
    
    // Create order
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, order_number, total_amount, payment_status, payment_id, shipping_address) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $order_number,
        $total,
        $data['payment_status'],
        $data['payment_id'] ?? null,
        $data['address']
    ]);
    
    $order_id = $pdo->lastInsertId();
    
    // Add order items
    foreach($cart_items as $item) {
        $price = $item['sale_price'] ?: $item['price'];
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $price]);
    }
    
    // Clear cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'order_number' => $order_number]);
    
} catch(Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Order failed: ' . $e->getMessage()]);
}
?>
<?php
require_once 'includes/auth.php';
header('Content-Type: application/json');

if(!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($product_id) {
    // Check if product already in cart
    $check = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $check->execute([$_SESSION['user_id'], $product_id]);
    $existing = $check->fetch();
    
    if($existing) {
        // Update quantity
        $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
        $stmt->execute([$existing['id']]);
    } else {
        // Insert new
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
}
?>
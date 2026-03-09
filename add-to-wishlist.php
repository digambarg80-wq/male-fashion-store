<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

header('Content-Type: application/json');

if(!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($product_id) {
    // Check if already in wishlist
    $check = $pdo->prepare("SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    $check->execute([$_SESSION['user_id'], $product_id]);
    
    if($check->fetch()) {
        // Remove from wishlist (toggle)
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist']);
    } else {
        // Add to wishlist
        $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $product_id]);
        echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
}
?>
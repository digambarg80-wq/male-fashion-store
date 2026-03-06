<?php
require_once 'includes/auth.php';

if(!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Insert into wishlist (ignore if already exists)
$stmt = $pdo->prepare("INSERT IGNORE INTO wishlist (user_id, product_id) VALUES (?, ?)");
if($stmt->execute([$_SESSION['user_id'], $product_id])) {
    echo json_encode(['success' => true, 'message' => 'Added to wishlist']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add']);
}
?>
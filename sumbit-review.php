<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

header('Content-Type: application/json');

if(!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to review']);
    exit;
}

$product_id = $_POST['product_id'] ?? 0;
$rating = $_POST['rating'] ?? 0;
$review_text = $_POST['review_text'] ?? '';

if($product_id && $rating && $review_text) {
    // Check if user already reviewed this product
    $check = $pdo->prepare("SELECT id FROM reviews WHERE product_id = ? AND user_id = ?");
    $check->execute([$product_id, $_SESSION['user_id']]);
    
    if($check->fetch()) {
        // Update existing review
        $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, review_text = ?, updated_at = NOW() WHERE product_id = ? AND user_id = ?");
        $success = $stmt->execute([$rating, $review_text, $product_id, $_SESSION['user_id']]);
    } else {
        // Insert new review
        $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
        $success = $stmt->execute([$product_id, $_SESSION['user_id'], $rating, $review_text]);
    }
    
    if($success) {
        echo json_encode(['success' => true, 'message' => 'Review submitted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to submit review']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
}
?>
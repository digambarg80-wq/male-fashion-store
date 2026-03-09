<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

header('Content-Type: application/json');

if(!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login to review']);
    exit;
}

$product_id = $_POST['product_id'] ?? 0;
$rating = $_POST['rating'] ?? 0;
$review_text = trim($_POST['review_text'] ?? '');

if(!$product_id || !$rating || !$review_text) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Check if user already reviewed this product
$check = $pdo->prepare("SELECT id FROM reviews WHERE product_id = ? AND user_id = ?");
$check->execute([$product_id, $_SESSION['user_id']]);
$existing = $check->fetch();

try {
    if($existing) {
        // Update existing review
        $stmt = $pdo->prepare("UPDATE reviews SET rating = ?, review_text = ?, updated_at = NOW() WHERE product_id = ? AND user_id = ?");
        $stmt->execute([$rating, $review_text, $product_id, $_SESSION['user_id']]);
        $message = "Review updated successfully!";
    } else {
        // Insert new review
        $stmt = $pdo->prepare("INSERT INTO reviews (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_id, $_SESSION['user_id'], $rating, $review_text]);
        $message = "Review submitted successfully!";
    }
    
    echo json_encode(['success' => true, 'message' => $message]);
    
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>
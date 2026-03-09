<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if(!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['order_id'] ?? 0;

// Get order items
$stmt = $pdo->prepare("SELECT oi.* FROM order_items oi WHERE oi.order_id = ?");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll();

// Clear current cart first (optional - remove if you want to add to existing cart)
$stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);

// Add items to cart
foreach($items as $item) {
    $check = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
    $check->execute([$_SESSION['user_id'], $item['product_id']]);
    
    if($check->fetch()) {
        // Update quantity if already in cart
        $update = $pdo->prepare("UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?");
        $update->execute([$item['quantity'], $_SESSION['user_id'], $item['product_id']]);
    } else {
        // Insert new
        $insert = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->execute([$_SESSION['user_id'], $item['product_id'], $item['quantity']]);
    }
}

header('Location: cart.php?reordered=1');
exit;
?>
<?php
require_once '../includes/auth.php';

if(!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

include '../includes/header.php';
?>

<div class="orders-container" style="max-width: 1000px; margin: 2rem auto; padding: 2rem;">
    <h1>My Orders</h1>
    
    <div style="background: #f5f5f5; padding: 3rem; text-align: center; border-radius: 8px;">
        <span class="material-icons" style="font-size: 60px; color: #999;">shopping_bag</span>
        <h3>No orders yet</h3>
        <p>When you place orders, they will appear here.</p>
        <a href="../products.php" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">Start Shopping</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
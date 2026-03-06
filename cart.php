<?php
require_once 'includes/auth.php';
include 'includes/header.php';
?>

<div class="cart-page" style="max-width: 1200px; margin: 2rem auto; padding: 2rem;">
    <h1>Shopping Cart</h1>
    
    <?php if(!isLoggedIn()): ?>
    <div style="background: #e3f2fd; padding: 1rem; margin-bottom: 2rem; border-radius: 4px;">
        <p>Please <a href="login.php" style="color: #1976d2;">login</a> to save your cart items</p>
    </div>
    <?php endif; ?>
    
    <div style="background: #f5f5f5; padding: 3rem; text-align: center; border-radius: 8px;">
        <span class="material-icons" style="font-size: 60px; color: #999;">shopping_cart</span>
        <h3>Your cart is empty</h3>
        <p>Looks like you haven't added anything yet.</p>
        <a href="products.php" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">Continue Shopping</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
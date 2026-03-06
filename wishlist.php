<?php
require_once 'includes/auth.php';
include 'includes/header.php';
?>

<div class="wishlist-page" style="max-width: 1200px; margin: 2rem auto; padding: 2rem;">
    <h1>My Wishlist</h1>
    
    <?php if(!isLoggedIn()): ?>
    <div style="background: #e3f2fd; padding: 1rem; margin-bottom: 2rem; border-radius: 4px;">
        <p>Please <a href="login.php" style="color: #1976d2;">login</a> to view your wishlist</p>
    </div>
    <?php endif; ?>
    
    <div style="background: #f5f5f5; padding: 3rem; text-align: center; border-radius: 8px;">
        <span class="material-icons" style="font-size: 60px; color: #999;">favorite_border</span>
        <h3>Your wishlist is empty</h3>
        <p>Save your favorite items here.</p>
        <a href="products.php" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">Browse Products</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
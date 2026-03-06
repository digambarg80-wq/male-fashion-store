<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
include 'includes/header.php';

$cart_items = [];
$total = 0;

if(isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.sale_price, p.image 
                           FROM cart c 
                           JOIN products p ON c.product_id = p.id 
                           WHERE c.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
    
    foreach($cart_items as $item) {
        $price = $item['sale_price'] ?: $item['price'];
        $total += $price * $item['quantity'];
    }
}
?>

<div class="cart-page" style="max-width: 1200px; margin: 2rem auto; padding: 2rem;">
    <h1>Shopping Cart</h1>
    
    <?php if(empty($cart_items)): ?>
        <div style="text-align: center; padding: 4rem; background: #f5f5f5; border-radius: 8px;">
            <span class="material-icons" style="font-size: 60px; color: #999;">shopping_cart</span>
            <h3>Your cart is empty</h3>
            <p>Looks like you haven't added anything yet.</p>
            <a href="/male-fashion-store/products.php" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">Continue Shopping</a>
        </div>
    <?php else: ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f5;">
                    <th style="padding: 1rem; text-align: left;">Product</th>
                    <th style="padding: 1rem; text-align: left;">Price</th>
                    <th style="padding: 1rem; text-align: left;">Quantity</th>
                    <th style="padding: 1rem; text-align: left;">Total</th>
                    <th style="padding: 1rem; text-align: left;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cart_items as $item): ?>
                <tr>
                    <td style="padding: 1rem;"><?php echo $item['name']; ?></td>
                    <td style="padding: 1rem;">₹<?php echo number_format($item['sale_price'] ?: $item['price']); ?></td>
                    <td style="padding: 1rem;"><?php echo $item['quantity']; ?></td>
                    <td style="padding: 1rem;">₹<?php echo number_format(($item['sale_price'] ?: $item['price']) * $item['quantity']); ?></td>
                    <td style="padding: 1rem;">
                        <a href="remove-from-cart.php?id=<?php echo $item['id']; ?>" style="color: #dc3545;">Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="padding: 1rem; text-align: right;"><strong>Total:</strong></td>
                    <td style="padding: 1rem;"><strong>₹<?php echo number_format($total); ?></strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        
        <div style="margin-top: 2rem; text-align: right;">
            <a href="/male-fashion-store/checkout.php" class="btn btn-primary">Proceed to Checkout</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
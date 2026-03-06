<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = getProduct($pdo, $product_id);

if (!$product) {
    header('Location: products.php');
    exit;
}
?>

<section class="product-detail">
    <div class="product-gallery">
        <img src="https://via.placeholder.com/800x1000?text=<?php echo urlencode($product['name']); ?>" alt="<?php echo $product['name']; ?>" class="main-image">
    </div>
    
    <div class="product-info-detail">
        <h1 class="product-title"><?php echo $product['name']; ?></h1>
        <p class="product-category"><?php echo $product['category_name']; ?></p>
        
        <div class="product-price-detail">
            <?php if($product['sale_price']): ?>
                <span class="sale-price">₹<?php echo number_format($product['sale_price']); ?></span>
                <span class="original-price">₹<?php echo number_format($product['price']); ?></span>
                <span style="color: var(--red); margin-left: 1rem;">Save ₹<?php echo number_format($product['price'] - $product['sale_price']); ?></span>
            <?php else: ?>
                <span>₹<?php echo number_format($product['price']); ?></span>
            <?php endif; ?>
        </div>
        
        <div class="product-description">
            <h3>Description</h3>
            <p><?php echo $product['description']; ?></p>
        </div>
        
        <div class="size-section">
            <h3>Select Size</h3>
            <div class="size-selector">
                <button class="size-btn">S</button>
                <button class="size-btn">M</button>
                <button class="size-btn">L</button>
                <button class="size-btn">XL</button>
                <button class="size-btn">XXL</button>
            </div>
        </div>
        
        <div class="quantity-section">
            <h3>Quantity</h3>
            <div class="quantity-selector">
                <button class="quantity-btn">-</button>
                <input type="number" value="1" min="1" max="<?php echo $product['stock']; ?>" readonly>
                <button class="quantity-btn">+</button>
            </div>
        </div>
        
        <button class="add-to-cart">Add to Cart</button>
        
        <div class="product-meta">
            <p><strong>Availability:</strong> <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?></p>
            <p><strong>Category:</strong> <?php echo $product['category_name']; ?></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
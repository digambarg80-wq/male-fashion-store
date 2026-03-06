<?php
require_once 'includes/db.php';
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
        <!-- This will show the image from the database -->
        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="main-image" style="width: 100%; height: auto; border-radius: 8px;">
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
                <?php if($product['category_id'] == 1): // Shoes ?>
                    <button class="size-btn">7</button>
                    <button class="size-btn">8</button>
                    <button class="size-btn">9</button>
                    <button class="size-btn">10</button>
                    <button class="size-btn">11</button>
                <?php elseif($product['category_id'] == 2): // Clothing ?>
                    <button class="size-btn">S</button>
                    <button class="size-btn">M</button>
                    <button class="size-btn">L</button>
                    <button class="size-btn">XL</button>
                    <button class="size-btn">XXL</button>
                <?php else: // Accessories ?>
                    <button class="size-btn">One Size</button>
                <?php endif; ?>
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
        
        <button class="add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
        
        <div class="product-meta">
            <p><strong>Availability:</strong> <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?></p>
            <p><strong>Category:</strong> <?php echo $product['category_name']; ?></p>
        </div>
    </div>
</section>

<script>
function addToCart(productId) {
    <?php if(isset($_SESSION['user_id'])): ?>
    fetch('/male-fashion-store/add-to-cart.php?id=' + productId)
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Product added to cart!');
            updateCartCount();
        }
    });
    <?php else: ?>
    if(confirm('Please login to add items to cart')) {
        window.location.href = '/male-fashion-store/login.php';
    }
    <?php endif; ?>
}
</script>

<?php include 'includes/footer.php'; ?>
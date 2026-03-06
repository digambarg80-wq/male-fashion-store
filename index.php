<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$featuredProducts = getFeaturedProducts($pdo, 8);
$categories = getAllCategories($pdo);
?>

<!-- Hero Section -->
<section class="hero" style="background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.pexels.com/photos/2529148/pexels-photo-2529148.jpeg'); background-size: cover; background-position: center; height: 600px; display: flex; align-items: center; color: white;">
    <div class="hero-content" style="max-width: 600px; margin-left: 10%;">
        <h1 style="font-size: 3.5rem; margin-bottom: 1rem;">NEW SEASON<br>JUST DROPPED</h1>
        <p style="font-size: 1.2rem; margin-bottom: 2rem;">Discover the latest collection of men's fashion. From classic sneakers to modern streetwear.</p>
        <a href="/male-fashion-store/products.php" class="cta-button" style="display: inline-block; padding: 1rem 2.5rem; background: white; color: black; text-decoration: none; font-weight: bold; border-radius: 30px;">Shop Now</a>
    </div>
</section>

<!-- Categories Section -->
<section class="categories-section">
    <h2 class="section-title">Shop by Category</h2>
    <div class="category-grid">
        <?php foreach($categories as $category): ?>
        <a href="/male-fashion-store/<?php echo strtolower($category['name']); ?>.php" class="category-card">
            <div class="category-image">
                <?php
                $icons = [
                    1 => 'sports_and_outdoors',
                    2 => 'checkroom',
                    3 => 'watch',
                    4 => 'sports_handball',
                    5 => 'star'
                ];
                $icon = $icons[$category['id']] ?? 'shopping_bag';
                ?>
                <span class="material-icons" style="font-size: 80px; color: #333;"><?php echo $icon; ?></span>
            </div>
            <h3><?php echo $category['name']; ?></h3>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section">
    <h2 class="section-title">Featured Products</h2>
    <div class="products-grid">
        <?php foreach($featuredProducts as $product): ?>
        <a href="/male-fashion-store/product-detail.php?id=<?php echo $product['id']; ?>" class="product-card">
            <div class="product-image">
                <?php if(!empty($product['image'])): ?>
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                        <span class="material-icons" style="font-size: 80px; color: white;">
                            <?php
                            $icons = [
                                1 => 'sports_and_outdoors',
                                2 => 'checkroom',
                                3 => 'watch',
                                4 => 'sports_handball',
                                5 => 'star'
                            ];
                            echo $icons[$product['category_id']] ?? 'shopping_bag';
                            ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <?php if($product['sale_price']): ?>
                <span class="product-badge">SALE</span>
                <?php endif; ?>
                
                <div class="product-overlay">
                    <button class="quick-view" onclick="event.preventDefault(); addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
                </div>
            </div>
            <div class="product-info">
                <h3><?php echo $product['name']; ?></h3>
                <p class="product-category"><?php echo $product['category_name']; ?></p>
                <div class="product-price">
                    <?php if($product['sale_price']): ?>
                        <span class="sale-price">₹<?php echo number_format($product['sale_price']); ?></span>
                        <span class="original-price">₹<?php echo number_format($product['price']); ?></span>
                    <?php else: ?>
                        <span>₹<?php echo number_format($product['price']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Features Section -->
<section class="features-section">
    <div class="features-container">
        <div class="feature">
            <span class="material-icons">local_shipping</span>
            <h3>Free Shipping</h3>
            <p>On orders above ₹999</p>
        </div>
        <div class="feature">
            <span class="material-icons">replay</span>
            <h3>30-Day Returns</h3>
            <p>Hassle-free returns</p>
        </div>
        <div class="feature">
            <span class="material-icons">security</span>
            <h3>Secure Payment</h3>
            <p>100% secure transactions</p>
        </div>
        <div class="feature">
            <span class="material-icons">support_agent</span>
            <h3>24/7 Support</h3>
            <p>Dedicated customer service</p>
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
            if(typeof updateCartCount === 'function') {
                updateCartCount();
            }
        }
    });
    <?php else: ?>
    if(confirm('Please login to add items to cart')) {
        window.location.href = '/male-fashion-store/login.php';
    }
    <?php endif; ?>
}
</script>

<?php 
// Only include footer once
include 'includes/footer.php'; 
?>
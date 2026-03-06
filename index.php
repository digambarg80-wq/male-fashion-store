<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$featuredProducts = getFeaturedProducts($pdo, 8);
$categories = getAllCategories($pdo);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>NEW SEASON<br>JUST DROPPED</h1>
        <p>Discover the latest collection of men's fashion. From classic sneakers to modern streetwear.</p>
        <a href="products.php" class="cta-button">Shop Now</a>
    </div>
</section>

<!-- Categories Section with Icons -->
<section class="categories-section">
    <h2 class="section-title">Shop by Category</h2>
    <div class="category-grid">
        <?php foreach($categories as $category): ?>
        <a href="products.php?category=<?php echo $category['id']; ?>" class="category-card">
            <div class="category-image" style="background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                <?php
                // Different icons for each category
                $icons = [
                    1 => 'shoes',      // Shoes
                    2 => 'checkroom',   // Clothing
                    3 => 'watch',       // Accessories
                    4 => 'sports_handball', // Sports Wear
                    5 => 'new_releases' // New Arrivals
                ];
                $icon = isset($icons[$category['id']]) ? $icons[$category['id']] : 'shopping_bag';
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
        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-card">
            <div class="product-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                <?php
                // Product icons based on category
                $product_icons = [
                    1 => 'sports_and_outdoors', // Shoes
                    2 => 'checkroom',            // Clothing
                    3 => 'watch',                 // Accessories
                    4 => 'sports_handball',       // Sports
                    5 => 'star'                    // New
                ];
                $product_icon = isset($product_icons[$product['category_id']]) ? $product_icons[$product['category_id']] : 'shopping_bag';
                ?>
                <span class="material-icons" style="font-size: 100px; color: white;"><?php echo $product_icon; ?></span>
                
                <?php if($product['sale_price']): ?>
                <span class="product-badge">SALE</span>
                <?php endif; ?>
                
                <div class="product-overlay">
                    <button class="quick-view">Quick View</button>
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

<?php include 'includes/footer.php'; ?>
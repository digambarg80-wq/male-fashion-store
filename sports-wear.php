<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Get sports wear (category_id = 4)
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.category_id = 4");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<style>
    .category-hero {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=1400');
        background-size: cover;
        background-position: center;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-align: center;
        margin-bottom: 2rem;
    }
    
    .category-hero h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
</style>

<!-- Category Hero Section -->
<div class="category-hero">
    <div>
        <h1>Sports Wear</h1>
        <p>Gear up for your workout</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <div>
        <span style="font-weight: bold;"><?php echo count($products); ?></span> products found
    </div>
    <div class="filter-options">
        <select class="filter-select">
            <option>Sort by: Featured</option>
            <option>Price: Low to High</option>
            <option>Price: High to Low</option>
        </select>
        <select class="filter-select">
            <option>Sport</option>
            <option>Running</option>
            <option>Training</option>
            <option>Basketball</option>
            <option>Football</option>
        </select>
    </div>
</div>

<!-- Products Grid -->
<section class="products-section">
    <?php if(empty($products)): ?>
    <div style="text-align: center; padding: 4rem;">
        <span class="material-icons" style="font-size: 80px; color: #999;">sports_handball</span>
        <h3>No sports wear found</h3>
        <p>Check back later for new arrivals.</p>
    </div>
    <?php else: ?>
    <div class="products-grid">
        <?php foreach($products as $product): ?>
        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-card">
            <div class="product-image">
                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); display: flex; align-items: center; justify-content: center;">
                    <span class="material-icons" style="font-size: 80px; color: white;">sports_handball</span>
                </div>
                <?php if($product['sale_price']): ?>
                <span class="product-badge">SALE</span>
                <?php endif; ?>
                <div class="product-overlay">
                    <button class="quick-view">Quick View</button>
                </div>
            </div>
            <div class="product-info">
                <h3><?php echo $product['name']; ?></h3>
                <p class="product-category">Sports Wear</p>
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
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
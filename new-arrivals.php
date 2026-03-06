<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Get new arrivals (category_id = 5)
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.category_id = 5");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<style>
    .category-hero {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1556906781-9a412961c28c?w=1400');
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
    
    .new-badge {
        background: #ff4444;
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
        display: inline-block;
    }
</style>

<!-- Category Hero Section -->
<div class="category-hero">
    <div>
        <h1>New Arrivals</h1>
        <p>Fresh drops just landed</p>
        <span class="new-badge" style="margin-top: 1rem;">NEW SEASON</span>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <div>
        <span style="font-weight: bold;"><?php echo count($products); ?></span> new products
    </div>
    <div class="filter-options">
        <select class="filter-select">
            <option>Sort by: Newest</option>
            <option>Price: Low to High</option>
            <option>Price: High to Low</option>
        </select>
    </div>
</div>

<!-- Products Grid -->
<section class="products-section">
    <?php if(empty($products)): ?>
    <div style="text-align: center; padding: 4rem;">
        <span class="material-icons" style="font-size: 80px; color: #999;">new_releases</span>
        <h3>No new arrivals yet</h3>
        <p>Check back soon for latest drops.</p>
    </div>
    <?php else: ?>
    <div class="products-grid">
        <?php foreach($products as $product): ?>
        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-card">
            <div class="product-image">
                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%); display: flex; align-items: center; justify-content: center;">
                    <span class="material-icons" style="font-size: 80px; color: white;">star</span>
                </div>
                <span class="product-badge" style="background: #ff4444;">NEW</span>
                <div class="product-overlay">
                    <button class="quick-view">Quick View</button>
                </div>
            </div>
            <div class="product-info">
                <h3><?php echo $product['name']; ?></h3>
                <p class="product-category">New Arrival</p>
                <div class="product-price">
                    <span>₹<?php echo number_format($product['price']); ?></span>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
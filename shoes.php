<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

// Get shoes (category_id = 1)
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                       FROM products p 
                       LEFT JOIN categories c ON p.category_id = c.id 
                       WHERE p.category_id = 1");
$stmt->execute();
$products = $stmt->fetchAll();

// Get category name
$cat_stmt = $pdo->query("SELECT name FROM categories WHERE id = 1");
$category = $cat_stmt->fetch();
?>

<style>
.category-hero {
    background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.pexels.com/photos/1082529/pexels-photo-1082529.jpeg');
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
        <h1><?php echo $category['name']; ?></h1>
        <p>Discover our collection of <?php echo strtolower($category['name']); ?></p>
    </div>
</div>

<!-- Products Grid -->
<section class="products-section">
    <div class="products-grid">
        <?php foreach($products as $product): ?>
        <a href="/male-fashion-store/product-detail.php?id=<?php echo $product['id']; ?>" class="product-card">
            <div class="product-image">
                <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
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

<?php include 'includes/footer.php'; ?>
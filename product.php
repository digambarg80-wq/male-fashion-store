<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';
include 'includes/header.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($search) {
    $products = searchProducts($pdo, $search);
    $pageTitle = "Search Results: " . $search;
} elseif ($category_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $products = $stmt->fetchAll();
    
    $catStmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
    $catStmt->execute([$category_id]);
    $category = $catStmt->fetch();
    $pageTitle = $category ? $category['name'] : 'Products';
} else {
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
    $products = $stmt->fetchAll();
    $pageTitle = 'All Products';
}
?>

<section class="products-section">
    <h2 class="section-title"><?php echo $pageTitle; ?></h2>
    
    <?php if(empty($products)): ?>
    <div style="text-align: center; padding: 4rem;">
        <span class="material-icons" style="font-size: 80px; color: #999;">search_off</span>
        <h3>No products found</h3>
        <p>Try checking back later or browse other categories.</p>
        <a href="products.php" class="btn btn-primary" style="display: inline-block; margin-top: 1rem;">View All Products</a>
    </div>
    <?php else: ?>
    <div class="products-grid">
        <?php foreach($products as $product): ?>
        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-card">
            <div class="product-image">
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
                <?php if($product['sale_price']): ?>
                <span class="product-badge">SALE</span>
                <?php endif; ?>
                <div class="product-overlay">
                    <button class="quick-view">Quick View</button>
                </div>
            </div>
            <div class="product-info">
                <h3><?php echo $product['name']; ?></h3>
                <p class="product-category"><?php echo $category['name'] ?? ''; ?></p>
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
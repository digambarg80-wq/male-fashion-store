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

<style>
    .product-detail {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 2rem;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 3rem;
    }
    
    .product-gallery img {
        width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .product-info-detail {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .product-title {
        font-size: 2rem;
        margin: 0;
    }
    
    .product-category {
        color: #666;
        margin: 0;
    }
    
    .product-price-detail {
        font-size: 1.5rem;
        font-weight: bold;
    }
    
    .sale-price {
        color: #dc3545;
        margin-right: 1rem;
    }
    
    .original-price {
        color: #999;
        text-decoration: line-through;
        font-size: 1.2rem;
    }
    
    .size-selector {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .size-btn {
        width: 50px;
        height: 50px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .size-btn:hover,
    .size-btn.active {
        background: #000;
        color: white;
        border-color: #000;
    }
    
    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .quantity-btn {
        width: 40px;
        height: 40px;
        border: 1px solid #ddd;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1.2rem;
    }
    
    .quantity-selector input {
        width: 60px;
        height: 40px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 8px;
    }
    
    .add-to-cart {
        padding: 1rem 2rem;
        background: #000;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .add-to-cart:hover {
        background: #333;
    }
    
    .product-meta {
        padding: 1rem 0;
        border-top: 1px solid #f0f0f0;
    }
    
    .reviews-section {
        grid-column: 1 / -1;
        margin-top: 2rem;
        padding: 2rem;
        background: #f8f9fa;
        border-radius: 12px;
    }
    
    .rating-summary {
        display: flex;
        align-items: center;
        gap: 2rem;
        margin-bottom: 2rem;
        padding: 1rem;
        background: white;
        border-radius: 8px;
    }
    
    .average-rating {
        text-align: center;
    }
    
    .average-number {
        font-size: 3rem;
        font-weight: bold;
        color: #667eea;
    }
    
    .stars {
        color: #ffc107;
        font-size: 1.2rem;
    }
    
    .review-form {
        display: none;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: white;
        border-radius: 8px;
    }
    
    .review-form.visible {
        display: block;
    }
    
    .star-rating {
        display: flex;
        gap: 0.5rem;
        font-size: 1.5rem;
        color: #ffc107;
        margin-bottom: 1rem;
    }
    
    .star-rating span {
        cursor: pointer;
    }
    
    .review-card {
        margin-bottom: 1rem;
        padding: 1rem;
        background: white;
        border-radius: 8px;
    }
    
    .review-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }
    
    .reviewer-name {
        font-weight: bold;
    }
    
    .review-date {
        color: #999;
        font-size: 0.9rem;
    }
    
    .review-rating {
        color: #ffc107;
        margin-bottom: 0.5rem;
    }
    
    .review-text {
        line-height: 1.6;
        color: #333;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .btn-outline {
        background: transparent;
        border: 2px solid #667eea;
        color: #667eea;
    }
    
    .btn-outline:hover {
        background: #667eea;
        color: white;
    }
    
    @media (max-width: 768px) {
        .product-detail {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="product-detail">
    <div class="product-gallery">
        <img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="main-image">
    </div>
    
    <div class="product-info-detail">
        <h1 class="product-title"><?php echo $product['name']; ?></h1>
        <p class="product-category"><?php echo $product['category_name']; ?></p>
        
        <div class="product-price-detail">
            <?php if($product['sale_price']): ?>
                <span class="sale-price">₹<?php echo number_format($product['sale_price']); ?></span>
                <span class="original-price">₹<?php echo number_format($product['price']); ?></span>
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
                <?php if($product['category_id'] == 1): ?>
                    <button class="size-btn">7</button>
                    <button class="size-btn">8</button>
                    <button class="size-btn">9</button>
                    <button class="size-btn">10</button>
                    <button class="size-btn">11</button>
                <?php elseif($product['category_id'] == 2): ?>
                    <button class="size-btn">S</button>
                    <button class="size-btn">M</button>
                    <button class="size-btn">L</button>
                    <button class="size-btn">XL</button>
                    <button class="size-btn">XXL</button>
                <?php else: ?>
                    <button class="size-btn">One Size</button>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="quantity-section">
            <h3>Quantity</h3>
            <div class="quantity-selector">
                <button class="quantity-btn" onclick="decreaseQuantity()">-</button>
                <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" readonly>
                <button class="quantity-btn" onclick="increaseQuantity(<?php echo $product['stock']; ?>)">+</button>
            </div>
        </div>
        
        <button class="add-to-cart" onclick="addToCart(<?php echo $product['id']; ?>)">Add to Cart</button>
        
        <div class="product-meta">
            <p><strong>Availability:</strong> <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?></p>
            <p><strong>Category:</strong> <?php echo $product['category_name']; ?></p>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="reviews-section">
        <h2>Customer Reviews</h2>
        
        <?php
        // Create reviews table if not exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
            id INT PRIMARY KEY AUTO_INCREMENT,
            product_id INT NOT NULL,
            user_id INT NOT NULL,
            rating INT CHECK (rating BETWEEN 1 AND 5),
            review_text TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        )");
        
        // Get average rating
        $avg_stmt = $pdo->prepare("SELECT AVG(rating) as avg, COUNT(*) as total FROM reviews WHERE product_id = ?");
        $avg_stmt->execute([$product_id]);
        $rating_data = $avg_stmt->fetch();
        
        // Get all reviews
        $reviews_stmt = $pdo->prepare("SELECT r.*, u.full_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
        $reviews_stmt->execute([$product_id]);
        $reviews = $reviews_stmt->fetchAll();
        ?>
        
        <!-- Rating Summary -->
        <div class="rating-summary">
            <div class="average-rating">
                <div class="average-number"><?php echo number_format($rating_data['avg'] ?? 0, 1); ?></div>
                <div class="stars">
                    <?php
                    $avg = round($rating_data['avg'] ?? 0);
                    for($i = 1; $i <= 5; $i++) {
                        echo $i <= $avg ? '★' : '☆';
                    }
                    ?>
                </div>
                <div>(<?php echo $rating_data['total'] ?? 0; ?> reviews)</div>
            </div>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <button onclick="toggleReviewForm()" class="btn btn-primary">
                    <span class="material-icons">rate_review</span>
                    Write a Review
                </button>
            <?php endif; ?>
        </div>
        
        <!-- Review Form -->
        <?php if(isset($_SESSION['user_id'])): ?>
        <div id="reviewForm" class="review-form">
            <h3>Write Your Review</h3>
            <form onsubmit="submitReview(event, <?php echo $product_id; ?>)">
                <div class="star-rating" id="starRating">
                    <span onclick="setRating(1)">☆</span>
                    <span onclick="setRating(2)">☆</span>
                    <span onclick="setRating(3)">☆</span>
                    <span onclick="setRating(4)">☆</span>
                    <span onclick="setRating(5)">☆</span>
                </div>
                <input type="hidden" name="rating" id="ratingValue" value="0">
                
                <div style="margin-bottom: 1rem;">
                    <textarea name="review_text" id="reviewText" rows="4" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 8px;" placeholder="Share your thoughts about this product..." required></textarea>
                </div>
                
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                    <button type="button" onclick="toggleReviewForm()" class="btn btn-outline">Cancel</button>
                </div>
            </form>
        </div>
        <?php endif; ?>
        
        <!-- Reviews List -->
        <?php if(empty($reviews)): ?>
            <p style="text-align: center; color: #999; padding: 2rem;">No reviews yet. Be the first to review this product!</p>
        <?php else: ?>
            <?php foreach($reviews as $review): ?>
            <div class="review-card">
                <div class="review-header">
                    <span class="reviewer-name"><?php echo $review['full_name']; ?></span>
                    <span class="review-date"><?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                </div>
                <div class="review-rating">
                    <?php for($i = 1; $i <= 5; $i++): ?>
                        <?php echo $i <= $review['rating'] ? '★' : '☆'; ?>
                    <?php endfor; ?>
                </div>
                <p class="review-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<script>
let currentRating = 0;

function setRating(rating) {
    currentRating = rating;
    document.getElementById('ratingValue').value = rating;
    const stars = document.querySelectorAll('#starRating span');
    stars.forEach((star, index) => {
        star.innerHTML = index < rating ? '★' : '☆';
    });
}

function toggleReviewForm() {
    const form = document.getElementById('reviewForm');
    form.classList.toggle('visible');
}

function submitReview(event, productId) {
    event.preventDefault();
    
    if(currentRating === 0) {
        alert('Please select a rating');
        return;
    }
    
    const reviewText = document.getElementById('reviewText').value;
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('rating', currentRating);
    formData.append('review_text', reviewText);
    
    fetch('submit-review.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Review submitted successfully!');
            location.reload();
        } else {
            alert(data.message);
        }
    });
}

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

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const value = parseInt(input.value);
    if(value > 1) {
        input.value = value - 1;
    }
}

function increaseQuantity(max) {
    const input = document.getElementById('quantity');
    const value = parseInt(input.value);
    if(value < max) {
        input.value = value + 1;
    }
}

// Size selector
document.querySelectorAll('.size-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
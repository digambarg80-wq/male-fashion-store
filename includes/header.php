<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mens Fashion Store - Nike Style</title>
    <link rel="stylesheet" href="/male-fashion-store/css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

 <!-- Favicon - Files in root folder -->
    <link rel="icon" type="image/x-icon" href="/male-fashion-store/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16" href="/male-fashion-store/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/male-fashion-store/favicon-32x32.png">
    <link rel="apple-touch-icon" href="/male-fashion-store/apple-touch-icon.png">
    
    <link rel="stylesheet" href="/male-fashion-store/css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">




    <style>



/* Search Suggestions */
.search-container {
    position: relative;
}

.suggestions-box {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    max-height: 400px;
    overflow-y: auto;
    display: none;
}

.suggestion-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.3s;
}

.suggestion-item:hover {
    background: #f5f5f5;
}

.suggestion-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.suggestion-icon .material-icons {
    color: white;
    font-size: 20px;
}

.suggestion-info {
    flex: 1;
}

.suggestion-name {
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.suggestion-price {
    color: #667eea;
    font-weight: 600;
    font-size: 0.9rem;
}

.no-results {
    padding: 1rem;
    text-align: center;
    color: #999;
}

        .user-menu {
            position: relative;
            display: inline-block;
        }
        
        .user-dropdown {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            min-width: 220px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            z-index: 1000;
            border-radius: 8px;
            padding: 0.5rem 0;
            margin-top: 10px;
        }
        
        .user-dropdown a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: #333;
            transition: background 0.3s;
        }
        
        .user-dropdown a:hover {
            background: #f5f5f5;
        }
        
        .user-dropdown hr {
            margin: 0.5rem 0;
            border: none;
            border-top: 1px solid #f0f0f0;
        }
        
        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #f44336;
            color: white;
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="nav-container">
                <div class="nav-left">
                    <div class="logo">
                         <a href="/male-fashion-store/index.php" style="text-decoration: none; color: #000; font-size: 24px; font-weight: bold; letter-spacing: 2px;">
        <span style="color: #667eea;">MEN'S</span> FASHION
    </a>
                    </div>
                    <ul class="nav-menu">
                        <li><a href="/male-fashion-store/index.php">Home</a></li>
                        <li><a href="/male-fashion-store/shoes.php">Shoes</a></li>
                        <li><a href="/male-fashion-store/clothing.php">Clothing</a></li>
                        <li><a href="/male-fashion-store/accessories.php">Accessories</a></li>
                        <li><a href="/male-fashion-store/sports-wear.php">Sports Wear</a></li>
                        <li><a href="/male-fashion-store/new-arrivals.php">New Arrivals</a></li>
                    </ul>
                </div>
                <div class="nav-right">
                    <div class="search-container">
    <form action="/male-fashion-store/products.php" method="GET" id="searchForm">
        <input type="text" name="search" id="searchInput" placeholder="Search products..." autocomplete="off">
        <button type="submit">
            <span class="material-icons">search</span>
        </button>
    </form>
    <div class="suggestions-box" id="suggestionsBox"></div>
</div>
                    <div class="nav-icons">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <div class="user-menu">
                                <a href="#" class="icon-link" id="userMenuBtn">
                                    <span class="material-icons">account_circle</span>
                                </a>
                                <div class="user-dropdown" id="userDropdown">
                                    <a href="/male-fashion-store/user/profile.php">
                                        <span class="material-icons">person</span>
                                        My Profile
                                    </a>
                                    <a href="/male-fashion-store/user/orders.php">
                                        <span class="material-icons">shopping_bag</span>
                                        My Orders
                                    </a>
                                    <a href="/male-fashion-store/wishlist.php">
                                        <span class="material-icons">favorite</span>
                                        Wishlist
                                    </a>
                                    <?php if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin'): ?>
                                    <hr>
                                    <a href="/male-fashion-store/admin/index.php">
                                        <span class="material-icons">admin_panel_settings</span>
                                        Admin Dashboard
                                    </a>
                                    <?php endif; ?>
                                    <hr>
                                    <a href="/male-fashion-store/logout.php" style="color: #dc3545;">
                                        <span class="material-icons">logout</span>
                                        Logout
                                    </a>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="/male-fashion-store/login.php" class="icon-link">
                                <span class="material-icons">person_outline</span>
                            </a>
                        <?php endif; ?>
                        
                        <a href="/male-fashion-store/wishlist.php" class="icon-link">
                            <span class="material-icons">favorite_border</span>
                        </a>
                        <a href="/male-fashion-store/cart.php" class="icon-link">
                            <span class="material-icons">shopping_bag</span>
                            <span class="cart-count" id="cartCount">0</span>
                        </a>
                    </div>
                </div>
                <div class="mobile-menu-btn">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>
    <main>

    <script>

// Live Search Suggestions
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const suggestionsBox = document.getElementById('suggestionsBox');
    let debounceTimer;

    if(searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value.trim();
            
            if(query.length < 2) {
                suggestionsBox.style.display = 'none';
                return;
            }
            
            debounceTimer = setTimeout(() => {
                fetch(`/male-fashion-store/search-suggestions.php?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    if(data.length > 0) {
                        let html = '';
                        data.forEach(item => {
                            html += `
                                <a href="${item.url}" class="suggestion-item">
                                    <div class="suggestion-icon">
                                        <span class="material-icons">${item.icon}</span>
                                    </div>
                                    <div class="suggestion-info">
                                        <div class="suggestion-name">${item.name}</div>
                                        <div class="suggestion-price">₹${item.price}</div>
                                    </div>
                                </a>
                            `;
                        });
                        suggestionsBox.innerHTML = html;
                        suggestionsBox.style.display = 'block';
                    } else {
                        suggestionsBox.innerHTML = '<div class="no-results">No products found</div>';
                        suggestionsBox.style.display = 'block';
                    }
                });
            }, 300);
        });
        
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if(!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = 'none';
            }
        });
    }
});

    document.addEventListener('DOMContentLoaded', function() {
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        
        if(userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                userDropdown.style.display = userDropdown.style.display === 'block' ? 'none' : 'block';
            });
            
            document.addEventListener('click', function(e) {
                if(!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.style.display = 'none';
                }
            });
        }
    });

    function updateCartCount() {
        fetch('/male-fashion-store/get-cart-count.php')
        .then(response => response.json())
        .then(data => {
            document.getElementById('cartCount').textContent = data.count;
        });
    }

    // Update cart count on page load
    updateCartCount();
    </script>
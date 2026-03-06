<?php
require_once '../includes/auth.php';

if(!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

require_once '../includes/db.php';

$user = getCurrentUser();
$message = '';
$error = '';

// Handle profile update
if(isset($_POST['update_profile'])) {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    
    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
    if($stmt->execute([$full_name, $phone, $address, $_SESSION['user_id']])) {
        $message = "Profile updated successfully!";
        $user = getCurrentUser(); // Refresh user data
    } else {
        $error = "Failed to update profile.";
    }
}

include '../includes/header.php';
?>

<style>
    .profile-wrapper {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 2rem;
    }
    
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 3rem 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }
    
    .profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .profile-header-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        gap: 2rem;
        flex-wrap: wrap;
    }
    
    .profile-avatar {
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid rgba(255,255,255,0.5);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    
    .profile-avatar .material-icons {
        font-size: 60px;
        color: white;
    }
    
    .profile-title h1 {
        margin: 0;
        font-size: 2rem;
        font-weight: 600;
    }
    
    .profile-title p {
        margin: 0.5rem 0 0;
        opacity: 0.9;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .profile-title p .material-icons {
        font-size: 1rem;
    }
    
    .profile-grid {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .profile-sidebar {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        height: fit-content;
    }
    
    .sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .sidebar-menu li {
        margin-bottom: 0.5rem;
    }
    
    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 10px;
        color: #666;
        text-decoration: none;
        transition: all 0.3s;
    }
    
    .sidebar-menu a:hover,
    .sidebar-menu a.active {
        background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
        color: #667eea;
    }
    
    .sidebar-menu a .material-icons {
        font-size: 1.2rem;
    }
    
    .profile-main {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    
    .profile-section {
        display: none;
    }
    
    .profile-section.active {
        display: block;
        animation: fadeIn 0.5s ease;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .section-title .material-icons {
        font-size: 2rem;
        color: #667eea;
    }
    
    .section-title h2 {
        margin: 0;
        font-size: 1.5rem;
        color: #333;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .info-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .info-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .info-label .material-icons {
        font-size: 1.2rem;
        color: #667eea;
    }
    
    .info-value {
        font-size: 1.2rem;
        font-weight: 600;
        color: #333;
        word-break: break-word;
    }
    
    .info-value.empty {
        color: #999;
        font-weight: normal;
        font-style: italic;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #555;
        font-weight: 500;
    }
    
    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
    }
    
    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }
    
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
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
    
    .alert {
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .stat-card .material-icons {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
    }
    
    .stat-card h3 {
        margin: 0;
        font-size: 2rem;
    }
    
    .stat-card p {
        margin: 0.5rem 0 0;
        opacity: 0.9;
    }
    
    @media (max-width: 768px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
        
        .profile-header-content {
            flex-direction: column;
            text-align: center;
        }
    }
</style>

<div class="profile-wrapper">
    <!-- Alert Messages -->
    <?php if($message): ?>
        <div class="alert alert-success">
            <span class="material-icons">check_circle</span>
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <?php if($error): ?>
        <div class="alert alert-error">
            <span class="material-icons">error</span>
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-header-content">
            <div class="profile-avatar">
                <span class="material-icons">person</span>
            </div>
            <div class="profile-title">
                <h1><?php echo $user['full_name']; ?></h1>
                <p>
                    <span class="material-icons">calendar_today</span>
                    Member since: <?php echo date('F Y', strtotime($user['created_at'])); ?>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Profile Grid -->
    <div class="profile-grid">
        <!-- Sidebar -->
        <div class="profile-sidebar">
            <ul class="sidebar-menu">
                <li>
                    <a href="#" onclick="showSection('overview'); return false;" class="active" id="menu-overview">
                        <span class="material-icons">dashboard</span>
                        Overview
                    </a>
                </li>
                <li>
                    <a href="#" onclick="showSection('profile'); return false;" id="menu-profile">
                        <span class="material-icons">person</span>
                        Profile Information
                    </a>
                </li>
                <li>
                    <a href="#" onclick="showSection('orders'); return false;" id="menu-orders">
                        <span class="material-icons">shopping_bag</span>
                        My Orders
                    </a>
                </li>
                <li>
                    <a href="#" onclick="showSection('wishlist'); return false;" id="menu-wishlist">
                        <span class="material-icons">favorite</span>
                        Wishlist
                    </a>
                </li>
                <li>
                    <a href="#" onclick="showSection('password'); return false;" id="menu-password">
                        <span class="material-icons">lock</span>
                        Change Password
                    </a>
                </li>
                <li>
                    <a href="../logout.php">
                        <span class="material-icons">logout</span>
                        Logout
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="profile-main">
            <!-- Overview Section -->
            <div id="section-overview" class="profile-section active">
                <div class="section-title">
                    <span class="material-icons">dashboard</span>
                    <h2>Account Overview</h2>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <span class="material-icons">shopping_bag</span>
                        <h3>0</h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="stat-card">
                        <span class="material-icons">favorite</span>
                        <h3>0</h3>
                        <p>Wishlist Items</p>
                    </div>
                    <div class="stat-card">
                        <span class="material-icons">star</span>
                        <h3>New</h3>
                        <p>Member Status</p>
                    </div>
                </div>
                
                <div class="info-grid">
                    <div class="info-card">
                        <div class="info-label">
                            <span class="material-icons">badge</span>
                            Full Name
                        </div>
                        <div class="info-value"><?php echo $user['full_name']; ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <span class="material-icons">person</span>
                            Username
                        </div>
                        <div class="info-value"><?php echo $user['username']; ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <span class="material-icons">email</span>
                            Email
                        </div>
                        <div class="info-value"><?php echo $user['email']; ?></div>
                    </div>
                    
                    <div class="info-card">
                        <div class="info-label">
                            <span class="material-icons">phone</span>
                            Phone
                        </div>
                        <div class="info-value <?php echo !$user['phone'] ? 'empty' : ''; ?>">
                            <?php echo $user['phone'] ?: 'Not provided'; ?>
                        </div>
                    </div>
                    
                    <div class="info-card" style="grid-column: 1/-1;">
                        <div class="info-label">
                            <span class="material-icons">location_on</span>
                            Address
                        </div>
                        <div class="info-value <?php echo !$user['address'] ? 'empty' : ''; ?>">
                            <?php echo $user['address'] ?: 'Not provided'; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Profile Edit Section -->
            <div id="section-profile" class="profile-section">
                <div class="section-title">
                    <span class="material-icons">edit</span>
                    <h2>Edit Profile</h2>
                </div>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="tel" name="phone" value="<?php echo $user['phone']; ?>" placeholder="Enter your phone number">
                    </div>
                    
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" placeholder="Enter your full address"><?php echo $user['address']; ?></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            <span class="material-icons">save</span>
                            Save Changes
                        </button>
                        <button type="button" class="btn btn-outline" onclick="showSection('overview')">
                            <span class="material-icons">cancel</span>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Orders Section -->
            <div id="section-orders" class="profile-section">
                <div class="section-title">
                    <span class="material-icons">shopping_bag</span>
                    <h2>My Orders</h2>
                </div>
                
                <div style="text-align: center; padding: 3rem; background: #f8f9fa; border-radius: 12px;">
                    <span class="material-icons" style="font-size: 60px; color: #999;">inbox</span>
                    <h3>No orders yet</h3>
                    <p>When you place orders, they will appear here.</p>
                    <a href="../products.php" class="btn btn-primary" style="display: inline-flex; margin-top: 1rem;">
                        <span class="material-icons">shopping_cart</span>
                        Start Shopping
                    </a>
                </div>
            </div>
            
            <!-- Wishlist Section -->
            <div id="section-wishlist" class="profile-section">
                <div class="section-title">
                    <span class="material-icons">favorite</span>
                    <h2>My Wishlist</h2>
                </div>
                
                <div style="text-align: center; padding: 3rem; background: #f8f9fa; border-radius: 12px;">
                    <span class="material-icons" style="font-size: 60px; color: #999;">favorite_border</span>
                    <h3>Your wishlist is empty</h3>
                    <p>Save your favorite items here.</p>
                    <a href="../products.php" class="btn btn-primary" style="display: inline-flex; margin-top: 1rem;">
                        <span class="material-icons">favorite</span>
                        Browse Products
                    </a>
                </div>
            </div>
            
            <!-- Change Password Section -->
            <div id="section-password" class="profile-section">
                <div class="section-title">
                    <span class="material-icons">lock</span>
                    <h2>Change Password</h2>
                </div>
                
                <form method="POST" action="change-password.php">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" name="change_password" class="btn btn-primary">
                            <span class="material-icons">lock_reset</span>
                            Update Password
                        </button>
                        <button type="button" class="btn btn-outline" onclick="showSection('overview')">
                            <span class="material-icons">cancel</span>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showSection(section) {
    // Hide all sections
    document.querySelectorAll('.profile-section').forEach(el => {
        el.classList.remove('active');
    });
    
    // Remove active class from all menu items
    document.querySelectorAll('.sidebar-menu a').forEach(el => {
        el.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById('section-' + section).classList.add('active');
    
    // Add active class to clicked menu item
    document.getElementById('menu-' + section).classList.add('active');
}

// Check URL hash for direct section access
if(window.location.hash) {
    const section = window.location.hash.substring(1);
    if(['overview', 'profile', 'orders', 'wishlist', 'password'].includes(section)) {
        showSection(section);
    }
}
</script>

<?php include '../includes/footer.php'; ?>  
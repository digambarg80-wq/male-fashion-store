<?php
require_once 'includes/auth.php';

// Redirect if not logged in
if(!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
$message = '';

// Handle profile update
if(isset($_POST['update_profile'])) {
    $result = updateUserProfile(
        $_SESSION['user_id'],
        $_POST['full_name'],
        $_POST['phone'],
        $_POST['address']
    );
    $message = $result['message'];
    $user = getCurrentUser(); // Refresh user data
}

// Handle password change
if(isset($_POST['change_password'])) {
    $result = changePassword(
        $_SESSION['user_id'],
        $_POST['old_password'],
        $_POST['new_password']
    );
    $message = $result['message'];
}

include 'includes/header.php';
?>

<div class="account-container" style="max-width: 1200px; margin: 2rem auto; padding: 2rem;">
    <h1>My Account</h1>
    
    <?php if($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 250px 1fr; gap: 2rem;">
        <!-- Sidebar -->
        <div style="background: #f5f5f5; padding: 1.5rem; border-radius: 8px;">
            <h3>Welcome, <?php echo $user['full_name']; ?></h3>
            <p>Member since: <?php echo date('M Y', strtotime($user['created_at'])); ?></p>
            <hr style="margin: 1rem 0;">
            <ul style="list-style: none; padding: 0;">
                <li style="margin-bottom: 0.5rem;">
                    <a href="#profile" onclick="showTab('profile')" style="text-decoration: none; color: #333;">Profile Information</a>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <a href="#orders" onclick="showTab('orders')" style="text-decoration: none; color: #333;">My Orders</a>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <a href="#password" onclick="showTab('password')" style="text-decoration: none; color: #333;">Change Password</a>
                </li>
                <li style="margin-bottom: 0.5rem;">
                    <a href="logout.php" style="text-decoration: none; color: #dc3545;">Logout</a>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div>
            <!-- Profile Tab -->
            <div id="profileTab">
                <h2>Profile Information</h2>
                <form method="POST" style="max-width: 600px;">
                    <div class="form-group">
                        <label>Full Name:</label>
                        <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" value="<?php echo $user['username']; ?>" disabled>
                        <small style="color: #666;">Username cannot be changed</small>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" value="<?php echo $user['email']; ?>" disabled>
                        <small style="color: #666;">Email cannot be changed</small>
                    </div>
                    <div class="form-group">
                        <label>Phone:</label>
                        <input type="tel" name="phone" value="<?php echo $user['phone']; ?>">
                    </div>
                    <div class="form-group">
                        <label>Address:</label>
                        <textarea name="address" rows="4"><?php echo $user['address']; ?></textarea>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                </form>
            </div>

            <!-- Orders Tab (Hidden by default) -->
            <div id="ordersTab" style="display: none;">
                <h2>My Orders</h2>
                <?php
                // You can add orders table and query here
                ?>
                <p>No orders yet.</p>
            </div>

            <!-- Password Tab (Hidden by default) -->
            <div id="passwordTab" style="display: none;">
                <h2>Change Password</h2>
                <form method="POST" style="max-width: 600px;">
                    <div class="form-group">
                        <label>Current Password:</label>
                        <input type="password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password:</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password:</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    document.getElementById('profileTab').style.display = 'none';
    document.getElementById('ordersTab').style.display = 'none';
    document.getElementById('passwordTab').style.display = 'none';
    
    if(tab === 'profile') document.getElementById('profileTab').style.display = 'block';
    if(tab === 'orders') document.getElementById('ordersTab').style.display = 'block';
    if(tab === 'password') document.getElementById('passwordTab').style.display = 'block';
    
    // Update URL hash
    window.location.hash = tab;
}

// Show correct tab based on URL hash
if(window.location.hash === '#orders') showTab('orders');
else if(window.location.hash === '#password') showTab('password');
else showTab('profile');
</script>

<style>
.form-group {
    margin-bottom: 1.5rem;
}
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}
.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}
.form-group input:disabled,
.form-group textarea:disabled {
    background: #f5f5f5;
    cursor: not-allowed;
}
</style>

<?php include 'includes/footer.php'; ?>
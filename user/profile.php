<?php
require_once '../includes/auth.php';

if(!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$user = getCurrentUser();
include '../includes/header.php';
?>

<div class="profile-container" style="max-width: 800px; margin: 2rem auto; padding: 2rem;">
    <h1>My Profile</h1>
    
    <div style="background: #f5f5f5; padding: 2rem; border-radius: 8px;">
        <div style="display: flex; align-items: center; gap: 2rem; margin-bottom: 2rem;">
            <div style="width: 100px; height: 100px; background: #000; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <span class="material-icons" style="font-size: 60px; color: white;">person</span>
            </div>
            <div>
                <h2><?php echo $user['full_name']; ?></h2>
                <p>Member since: <?php echo date('F Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>
        
        <div style="display: grid; gap: 1rem;">
            <div style="padding: 1rem; background: white; border-radius: 4px;">
                <strong>Username:</strong> <?php echo $user['username']; ?>
            </div>
            <div style="padding: 1rem; background: white; border-radius: 4px;">
                <strong>Email:</strong> <?php echo $user['email']; ?>
            </div>
            <div style="padding: 1rem; background: white; border-radius: 4px;">
                <strong>Phone:</strong> <?php echo $user['phone'] ?: 'Not provided'; ?>
            </div>
            <div style="padding: 1rem; background: white; border-radius: 4px;">
                <strong>Address:</strong> <?php echo $user['address'] ?: 'Not provided'; ?>
            </div>
        </div>
        
        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
            <a href="change-password.php" class="btn">Change Password</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
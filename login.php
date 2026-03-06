<?php
require_once 'includes/auth.php';

$error = '';
$success = '';

if(isset($_POST['login'])) {
    $result = loginUser($_POST['username'], $_POST['password']);
    if($result['success']) {
        if($result['user_type'] === 'admin') {
            header('Location: /male-fashion-store/admin/index.php');
        } else {
            header('Location: /male-fashion-store/user/profile.php');
        }
        exit;
    } else {
        $error = $result['message'];
    }
}

if(isset($_POST['register'])) {
    if($_POST['password'] !== $_POST['confirm_password']) {
        $error = "Passwords do not match";
    } else {
        $result = registerUser(
            $_POST['username'],
            $_POST['email'],
            $_POST['password'],
            $_POST['full_name'],
            $_POST['phone'] ?? '',
            $_POST['address'] ?? ''
        );
        if($result['success']) {
            $success = $result['message'];
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<style>
.auth-wrapper {
    min-height: 80vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.auth-container {
    width: 100%;
    max-width: 450px;
}
.auth-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    overflow: hidden;
}
.auth-header {
    padding: 2rem 2rem 1rem;
    text-align: center;
}
.auth-tabs {
    display: flex;
    padding: 0 2rem;
    gap: 1rem;
    border-bottom: 2px solid #f0f0f0;
}
.auth-tab {
    flex: 1;
    padding: 1rem;
    background: none;
    border: none;
    font-size: 1.1rem;
    font-weight: 600;
    color: #999;
    cursor: pointer;
}
.auth-tab.active {
    color: #667eea;
    border-bottom: 2px solid #667eea;
}
.auth-forms {
    padding: 2rem;
}
.auth-form {
    display: none;
}
.auth-form.active {
    display: block;
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
}
.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
}
.auth-btn {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
}
.alert {
    padding: 1rem;
    margin: 1rem 2rem;
    border-radius: 10px;
}
.alert-error {
    background: #fee;
    color: #c33;
}
.alert-success {
    background: #efe;
    color: #3c6;
}
</style>

<div class="auth-wrapper">
    <div class="auth-container">
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome</h1>
                <p>Sign in to continue</p>
            </div>
            
            <div class="auth-tabs">
                <button class="auth-tab active" onclick="switchTab('login')">Login</button>
                <button class="auth-tab" onclick="switchTab('register')">Register</button>
            </div>
            
            <div class="auth-forms">
                <!-- Login Form -->
                <form id="login-form" class="auth-form active" method="POST">
                    <div class="form-group">
                        <label>Username or Email</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="auth-btn">Login</button>
                </form>
                
                <!-- Register Form -->
                <form id="register-form" class="auth-form" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="register" class="auth-btn">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
    
    if(tab === 'login') {
        document.querySelectorAll('.auth-tab')[0].classList.add('active');
        document.getElementById('login-form').classList.add('active');
    } else {
        document.querySelectorAll('.auth-tab')[1].classList.add('active');
        document.getElementById('register-form').classList.add('active');
    }
}
</script>

<?php include 'includes/footer.php'; ?>
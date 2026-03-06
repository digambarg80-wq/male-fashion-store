<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';

// Handle login
if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['full_name'] = $user['full_name'];
        
        if($user['user_type'] === 'admin') {
            header('Location: admin/index.php');
        } else {
            header('Location: user/profile.php');
        }
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}

// Handle registration
if(isset($_POST['register'])) {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if username or email exists
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        
        if($check->fetch()) {
            $error = "Username or email already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, user_type) VALUES (?, ?, ?, ?, 'customer')");
            
            if($stmt->execute([$username, $email, $hashed_password, $full_name])) {
                $success = "Registration successful! Please login.";
            } else {
                $error = "Registration failed!";
            }
        }
    }
}

include 'includes/header.php';
?>

<style>
    .auth-wrapper {
        min-height: calc(100vh - 200px);
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
    
    .auth-header h1 {
        margin: 0;
        font-size: 2rem;
        color: #333;
    }
    
    .auth-header p {
        color: #666;
        margin: 0.5rem 0 0;
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
        transition: all 0.3s;
        position: relative;
    }
    
    .auth-tab.active {
        color: #667eea;
    }
    
    .auth-tab.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 2px;
        background: #667eea;
    }
    
    .auth-forms {
        padding: 2rem;
    }
    
    .auth-form {
        display: none;
    }
    
    .auth-form.active {
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
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        color: #555;
        font-weight: 500;
        font-size: 0.95rem;
    }
    
    .input-group {
        position: relative;
        display: flex;
        align-items: center;
    }
    
    .input-icon {
        position: absolute;
        left: 1rem;
        color: #999;
    }
    
    .input-group input {
        width: 100%;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s;
        background: #f8f9fa;
    }
    
    .input-group input:focus {
        outline: none;
        border-color: #667eea;
        background: white;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .auth-btn {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .auth-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .auth-footer {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #f0f0f0;
        color: #666;
    }
    
    .auth-footer a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }
    
    .auth-footer a:hover {
        text-decoration: underline;
    }
    
    .alert {
        padding: 1rem;
        margin: 1rem 2rem;
        border-radius: 10px;
        font-size: 0.95rem;
    }
    
    .alert-error {
        background: #fee;
        color: #c33;
        border: 1px solid #fcc;
    }
    
    .alert-success {
        background: #efe;
        color: #3c6;
        border: 1px solid #cfc;
    }
    
    .social-login {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    
    .social-btn {
        flex: 1;
        padding: 0.8rem;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        background: white;
        font-size: 0.95rem;
        font-weight: 500;
        color: #555;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .social-btn:hover {
        background: #f8f9fa;
        border-color: #999;
    }
    
    .social-btn img {
        width: 20px;
        height: 20px;
    }
</style>

<div class="auth-wrapper">
    <div class="auth-container">
        <!-- Alert Messages -->
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <!-- Auth Card -->
        <div class="auth-card">
            <div class="auth-header">
                <h1>Welcome Back</h1>
                <p>Sign in to continue to Fashion Store</p>
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
                        <div class="input-group">
                            <span class="material-icons input-icon">person</span>
                            <input type="text" name="username" placeholder="Enter your username or email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <span class="material-icons input-icon">lock</span>
                            <input type="password" name="password" placeholder="Enter your password" required>
                        </div>
                    </div>
                    
                    <div style="text-align: right; margin-bottom: 1.5rem;">
                        <a href="#" style="color: #667eea; text-decoration: none; font-size: 0.9rem;">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" name="login" class="auth-btn">
                        <span class="material-icons" style="vertical-align: middle; margin-right: 0.5rem;">login</span>
                        Login
                    </button>
                    
                    <div class="social-login">
                        <button type="button" class="social-btn">
                            <span>G</span> Google
                        </button>
                        <button type="button" class="social-btn">
                            <span>f</span> Facebook
                        </button>
                    </div>
                </form>
                
                <!-- Register Form -->
                <form id="register-form" class="auth-form" method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <div class="input-group">
                            <span class="material-icons input-icon">badge</span>
                            <input type="text" name="full_name" placeholder="Enter your full name" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Username</label>
                        <div class="input-group">
                            <span class="material-icons input-icon">person</span>
                            <input type="text" name="username" placeholder="Choose a username" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <div class="input-group">
                            <span class="material-icons input-icon">email</span>
                            <input type="email" name="email" placeholder="Enter your email" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Password</label>
                        <div class="input-group">
                            <span class="material-icons input-icon">lock</span>
                            <input type="password" name="password" placeholder="Create a password" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <div class="input-group">
                            <span class="material-icons input-icon">lock</span>
                            <input type="password" name="confirm_password" placeholder="Confirm your password" required>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 1.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: #666;">
                            <input type="checkbox" required> 
                            I agree to the <a href="#" style="color: #667eea;">Terms & Conditions</a>
                        </label>
                    </div>
                    
                    <button type="submit" name="register" class="auth-btn">
                        <span class="material-icons" style="vertical-align: middle; margin-right: 0.5rem;">person_add</span>
                        Create Account
                    </button>
                </form>
            </div>
            
            <div class="auth-footer">
                <p>By continuing, you agree to our <a href="#">Terms</a> and <a href="#">Privacy Policy</a></p>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.auth-form');
    
    tabs.forEach(t => t.classList.remove('active'));
    forms.forEach(f => f.classList.remove('active'));
    
    if(tab === 'login') {
        tabs[0].classList.add('active');
        document.getElementById('login-form').classList.add('active');
    } else {
        tabs[1].classList.add('active');
        document.getElementById('register-form').classList.add('active');
    }
}

// Switch to register tab if there's an error in registration
<?php if(isset($_POST['register']) && $error): ?>
switchTab('register');
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
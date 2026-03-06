<?php
require_once 'db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function getCurrentUser() {
    global $pdo;
    if(isLoggedIn()) {
        $stmt = $pdo->prepare("SELECT id, username, email, full_name, phone, address, user_type, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

function loginUser($username, $password) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();
    
    if($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['full_name'] = $user['full_name'];
        return ['success' => true, 'user_type' => $user['user_type']];
    }
    return ['success' => false, 'message' => 'Invalid username or password'];
}

function registerUser($username, $email, $password, $full_name, $phone = '', $address = '') {
    global $pdo;
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if($stmt->fetch()) {
        return ['success' => false, 'message' => 'Username or email already exists'];
    }
    
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, phone, address, user_type) VALUES (?, ?, ?, ?, ?, ?, 'customer')");
    
    if($stmt->execute([$username, $email, $hashed_password, $full_name, $phone, $address])) {
        return ['success' => true, 'message' => 'Registration successful!'];
    }
    return ['success' => false, 'message' => 'Registration failed'];
}

function logoutUser() {
    session_destroy();
    return ['success' => true];
}
?>
<?php
require_once 'includes/db.php';

$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$email = 'admin@fashionstore.com';
$full_name = 'Administrator';

$stmt = $pdo->prepare("INSERT INTO users (username, email, password, full_name, user_type) VALUES (?, ?, ?, ?, 'admin')");
if($stmt->execute([$username, $email, $password, $full_name])) {
    echo "Admin created successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
} else {
    echo "Failed to create admin";
}
?>
<?php
require_once 'includes/auth.php';
header('Content-Type: application/json');

if(isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as count FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    echo json_encode(['count' => $result['count'] ?? 0]);
} else {
    echo json_encode(['count' => 0]);
}
?>
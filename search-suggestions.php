<?php
require_once 'includes/db.php';

$query = isset($_GET['q']) ? $_GET['q'] : '';
$response = [];

if(strlen($query) >= 2) {
    $search = "%{$query}%";
    $stmt = $pdo->prepare("SELECT id, name, price, sale_price, category_id 
                           FROM products 
                           WHERE name LIKE ? OR description LIKE ? 
                           LIMIT 5");
    $stmt->execute([$search, $search]);
    $products = $stmt->fetchAll();
    
    foreach($products as $product) {
        $icons = [
            1 => 'sports_and_outdoors',
            2 => 'checkroom',
            3 => 'watch',
            4 => 'sports_handball',
            5 => 'star'
        ];
        
        $response[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['sale_price'] ?: $product['price'],
            'icon' => $icons[$product['category_id']] ?? 'shopping_bag',
            'url' => "/male-fashion-store/product-detail.php?id=" . $product['id']
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
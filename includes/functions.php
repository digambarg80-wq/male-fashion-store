<?php
// Database helper functions

function getFeaturedProducts($pdo, $limit = 8) {
    // Fixed version - no more error
    $stmt = $pdo->query("SELECT p.*, c.name as category_name 
                         FROM products p 
                         LEFT JOIN categories c ON p.category_id = c.id 
                         WHERE p.featured = TRUE 
                         ORDER BY p.created_at DESC 
                         LIMIT " . $limit);
    return $stmt->fetchAll();
}

function getProductsByCategory($pdo, $category_id) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.category_id = ?");
    $stmt->execute([$category_id]);
    return $stmt->fetchAll();
}

function getProduct($pdo, $id) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

function getAllCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

function searchProducts($pdo, $query) {
    $search = "%{$query}%";
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                           FROM products p 
                           LEFT JOIN categories c ON p.category_id = c.id 
                           WHERE p.name LIKE ? OR p.description LIKE ?");
    $stmt->execute([$search, $search]);
    return $stmt->fetchAll();
}
?>
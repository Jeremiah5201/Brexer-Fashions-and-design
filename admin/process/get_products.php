<?php
header('Content-Type: application/json');
require_once '../../includes/db_connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Fix image paths to be relative to the admin area
    foreach ($products as &$product) {
        if (!empty($product['image_path']) && strpos($product['image_path'], 'http') !== 0) {
            $product['image_path'] = '../' . ltrim($product['image_path'], '/');
        }
    }
    
    echo json_encode($products);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch products']);
}
?>

<?php
session_start();
require_once '../../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('Product ID is required');
    }
    
    $product_id = intval($_GET['id']);
    
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        throw new Exception('Product not found');
    }
    
    // Add default stock if column doesn't exist
    if (!isset($product['stock'])) {
        $product['stock'] = 0;
    }
    
    // Add full image path (relative to admin folder)
    if (!empty($product['image_path'])) {
        $product['image_path'] = '../' . $product['image_path'];
    }
    
    echo json_encode($product);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
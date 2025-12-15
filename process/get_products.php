<?php
header('Content-Type: application/json');

require_once '../includes/db_connect.php';

try {
    $stmt = $pdo->prepare("SELECT id, name, description, price, image_path, category FROM products ORDER BY created_at DESC");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($products);
} catch (PDOException $e) {
    http_response_code(500);
    // Do not expose internal error details in production
    echo json_encode(['error' => 'Failed to fetch products']);
}

<?php
session_start();
require_once '../../includes/db_connect.php';

header('Content-Type: application/json');

// Debug logging
error_log("Product handler called");
error_log("POST data: " . print_r($_POST, true));
error_log("FILES data: " . print_r($_FILES, true));

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$response = ['success' => false, 'error' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $action = $_POST['action'] ?? 'upload';
    $product_id = $_POST['product_id'] ?? null;

    // Debug
    error_log("Action: $action");
    error_log("Product ID: $product_id");

    // Validate required fields
    $required_fields = ['product_name', 'description', 'price', 'category'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("Please fill in all required fields. Missing: $field");
        }
    }

    // Check if stock exists in POST, default to 0
    $stock = isset($_POST['stock']) ? intval($_POST['stock']) : 0;

    // Handle file upload
    $image_path = null;
    $has_new_image = false;
    
    // Check if image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $has_new_image = true;
        
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'];
        $file_type = mime_content_type($file['tmp_name']);
        
        if (!in_array($file_type, $allowed_types)) {
            throw new Exception('Only JPG, PNG, WEBP, and GIF images are allowed');
        }
        
        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception('Image size should not exceed 5MB');
        }
        
        // Generate unique filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $upload_dir = '../../uploads/products/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $destination = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $image_path = 'uploads/products/' . $filename;
            
            // If updating and has old image, delete old image
            if ($action === 'update' && $product_id && !empty($_POST['old_image_path'])) {
                $old_image = '../../' . $_POST['old_image_path'];
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
        } else {
            throw new Exception('Failed to upload image. Please try again.');
        }
    } elseif ($action === 'update' && empty($_FILES['image']['name'])) {
        // Keep existing image if no new image uploaded during update
        $image_path = $_POST['old_image_path'] ?? null;
    } elseif ($action === 'upload') {
        // For new uploads, image is required
        throw new Exception('Please select a product image');
    }

    // Prepare product data
    $product_data = [
        'name' => trim($_POST['product_name']),
        'description' => trim($_POST['description']),
        'price' => floatval($_POST['price']),
        'category' => $_POST['category'],
        'stock' => $stock
    ];

    // Debug
    error_log("Product data prepared: " . print_r($product_data, true));

    if ($action === 'update' && $product_id) {
        // Update existing product
        if ($has_new_image) {
            $sql = "UPDATE products SET 
                    name = :name,
                    description = :description,
                    price = :price,
                    category = :category,
                    stock = :stock,
                    image_path = :image_path,
                    updated_at = NOW()
                    WHERE id = :id";
            $product_data['image_path'] = $image_path;
        } else {
            $sql = "UPDATE products SET 
                    name = :name,
                    description = :description,
                    price = :price,
                    category = :category,
                    stock = :stock,
                    updated_at = NOW()
                    WHERE id = :id";
        }
        
        $product_data['id'] = $product_id;
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($product_data);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception('Database error: ' . $errorInfo[2]);
        }
        
        // Get updated product data
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $updated_product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response['success'] = true;
        $response['action'] = 'update';
        $response['product'] = $updated_product;
        if ($updated_product['image_path']) {
            $response['product']['image_path'] = '../' . $updated_product['image_path'];
        }
        
    } else {
        // Insert new product - image is required for new uploads
        if (!$image_path) {
            throw new Exception('Please select a product image');
        }
        
        $sql = "INSERT INTO products (name, description, price, category, stock, image_path) 
                VALUES (:name, :description, :price, :category, :stock, :image_path)";
        
        $product_data['image_path'] = $image_path;
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($product_data);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception('Database error: ' . $errorInfo[2]);
        }
        
        $product_id = $pdo->lastInsertId();
        
        // Get the inserted product
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $new_product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $response['success'] = true;
        $response['action'] = 'upload';
        $response['product'] = $new_product;
        if ($new_product['image_path']) {
            $response['product']['image_path'] = '../' . $new_product['image_path'];
        }
    }

} catch (Exception $e) {
    error_log("Error in product_handler: " . $e->getMessage());
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
?>
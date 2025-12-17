<?php
session_start();
require_once '../../includes/db_connect.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$product_id   = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$product_name = $_POST['product_name'] ?? '';
$description  = $_POST['description'] ?? '';
$price        = $_POST['price'] ?? '';
$category     = $_POST['category'] ?? '';

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
    exit;
}

if ($product_name === '' || $description === '' || $category === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

if (!is_numeric($price)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid price']);
    exit;
}

$image_path = null;
if (isset($_FILES['image']) && is_uploaded_file($_FILES['image']['tmp_name'])) {
    $target_dir = "../../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $originalName = $_FILES["image"]["name"];
    $imageFileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedExtensions, true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Only JPG, JPEG, PNG & GIF are allowed']);
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES["image"]["tmp_name"]);
    finfo_close($finfo);
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mimeType, $allowedMimeTypes, true)) {
        http_response_code(400);
        echo json_encode(['error' => 'Uploaded file is not a valid image']);
        exit;
    }

    if ($_FILES["image"]["size"] > 5000000) {
        http_response_code(400);
        echo json_encode(['error' => 'File too large (max 5MB)']);
        exit;
    }

    $safeName = bin2hex(random_bytes(16)) . '.' . $imageFileType;
    $target_file = $target_dir . $safeName;
    if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to upload image']);
        exit;
    }
    $image_path = "uploads/" . $safeName;

    // Optional cleanup of previous image
    try {
        $stmtOld = $pdo->prepare('SELECT image_path FROM products WHERE id = ?');
        $stmtOld->execute([$product_id]);
        $old = $stmtOld->fetch(PDO::FETCH_ASSOC);
        if ($old && !empty($old['image_path'])) {
            $old_abs = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $old['image_path']);
            if (file_exists($old_abs)) {
                @unlink($old_abs);
            }
        }
    } catch (PDOException $e) {}
}

try {
    if ($image_path) {
        $sql = 'UPDATE products SET name = ?, description = ?, price = ?, category = ?, image_path = ? WHERE id = ?';
        $params = [$product_name, $description, $price, $category, $image_path, $product_id];
    } else {
        $sql = 'UPDATE products SET name = ?, description = ?, price = ?, category = ? WHERE id = ?';
        $params = [$product_name, $description, $price, $category, $product_id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    header('Location: ../dashboard.php?updated=1');
    exit();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update product']);
    exit;
}
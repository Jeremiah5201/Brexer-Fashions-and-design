<?php
session_start();
require_once '../../includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    
    // Handle file upload
    $target_dir = "../../uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Generate a safe random filename preserving extension
    $originalName = $_FILES["image"]["name"];
    $imageFileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($imageFileType, $allowedExtensions, true)) {
        die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    }

    $safeName = bin2hex(random_bytes(16)) . '.' . $imageFileType;
    $target_file = $target_dir . $safeName;
    $image_path = "uploads/" . $safeName;
    
    // Check if image file is actual image using finfo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES["image"]["tmp_name"]);
    finfo_close($finfo);

    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mimeType, $allowedMimeTypes, true)) {
        die("Uploaded file is not a valid image.");
    }

    // Check file size (limit to 5MB)
    if ($_FILES["image"]["size"] > 5000000) {
        die("Sorry, your file is too large.");
    }
    
    // Upload file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        // Insert into database
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image_path, category) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$product_name, $description, $price, $image_path, $category])) {
            header('Location: ../dashboard.php?success=1');
            exit();
        } else {
            echo "Error saving to database.";
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
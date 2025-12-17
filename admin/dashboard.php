<?php
session_start();

// Proper authentication check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Database connection
require_once '../includes/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BREXERS FASHIONS</title>
    <link rel="stylesheet" href="../css/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Admin Dashboard Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .admin-sidebar {
            width: 250px;
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h2 {
            font-size: 1.5em;
            margin-bottom: 5px;
            color: #ecf0f1;
        }
        
        .sidebar-header p {
            color: #bdc3c7;
            font-size: 0.9em;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            color: #ecf0f1;
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .sidebar-nav a:hover {
            background: rgba(255,255,255,0.1);
            border-left-color: #3498db;
        }
        
        .sidebar-nav a.active {
            background: rgba(52, 152, 219, 0.2);
            border-left-color: #3498db;
            font-weight: 500;
        }
        
        .sidebar-nav i {
            width: 20px;
            text-align: center;
        }
        
        /* Main Content Styles */
        .admin-main {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            min-height: 100vh;
        }
        
        .admin-header {
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-header h1 {
            font-size: 1.8em;
            color: #2c3e50;
            margin: 0;
        }
        
        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Form Styles */
        .upload-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 30px;
            margin-bottom: 40px;
        }
        
        .upload-form {
            max-width: 800px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.95em;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e8ed;
            border-radius: 6px;
            font-size: 1em;
            transition: border-color 0.3s ease;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        /* File Upload Styles */
        .file-upload {
            position: relative;
        }
        
        .file-upload input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }
        
        .file-preview {
            border: 3px dashed #e1e8ed;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            background: #f8fafc;
            transition: all 0.3s ease;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        
        .file-preview:hover {
            border-color: #3498db;
            background: #f0f7ff;
        }
        
        .file-preview i {
            font-size: 3em;
            color: #95a5a6;
            margin-bottom: 15px;
        }
        
        .file-preview p {
            color: #7f8c8d;
            margin: 5px 0;
        }
        
        #currentImage {
            max-width: 100%;
            max-height: 200px;
            border-radius: 6px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 15px 0;
        }
        
        /* Button Styles */
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .upload-btn,
        .cancel-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .upload-btn {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }
        
        .upload-btn:hover {
            background: linear-gradient(135deg, #2980b9 0%, #1f639b 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .cancel-btn {
            background: #95a5a6;
            color: white;
        }
        
        .cancel-btn:hover {
            background: #7f8c8d;
            transform: translateY(-2px);
        }
        
        /* Products Grid */
        .products-section {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        
        .products-section h2 {
            color: #2c3e50;
            margin-bottom: 25px;
            font-size: 1.5em;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f2f5;
        }
        
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .product-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #e1e8ed;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .product-image {
            width: 100%;
            height: 200px;
            overflow: hidden;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-image img {
            transform: scale(1.1);
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-info h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
            font-size: 1.1em;
            line-height: 1.4;
            height: 2.8em;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-size: 0.9em;
        }
        
        .product-category {
            background: #e8f4fc;
            color: #3498db;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .product-price {
            font-weight: 700;
            color: #27ae60;
            font-size: 1.2em;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            flex: 1;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 0.9em;
            transition: all 0.2s ease;
        }
        
        .btn i {
            font-size: 0.9em;
        }
        
        .btn-edit {
            background-color: #3498db;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        
        /* No Products Message */
        .no-products {
            text-align: center;
            padding: 50px 20px;
            color: #7f8c8d;
            font-size: 1.1em;
        }
        
        .no-products i {
            font-size: 3em;
            margin-bottom: 20px;
            color: #bdc3c7;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .admin-sidebar {
                width: 200px;
            }
            
            .admin-main {
                margin-left: 200px;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }
            
            .admin-sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .upload-btn,
            .cancel-btn {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .admin-main {
                padding: 15px;
            }
            
            .products-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Admin Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>BREXERS ADMIN</h2>
                <p>Dashboard</p>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="#upload">
                    <i class="fas fa-upload"></i> Upload Products
                </a>
                <a href="#products">
                    <i class="fas fa-box"></i> Manage Products
                </a>
                <a href="../index.php" target="_blank">
                    <i class="fas fa-home"></i> View Site
                </a>
                <a href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1 id="pageTitle">Upload Products</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?></span>
                </div>
            </header>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php 
                    echo htmlspecialchars($_SESSION['success']); 
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php 
                    echo htmlspecialchars($_SESSION['error']); 
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

           <section class="upload-section" id="upload">
    <div class="upload-container">
        <form id="productForm" action="process/product_handler.php" method="POST" enctype="multipart/form-data" class="upload-form">
            <input type="hidden" id="productId" name="product_id" value="">
            <input type="hidden" id="actionType" name="action" value="upload">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="productName">Product Name *</label>
                    <input type="text" id="productName" name="product_name" required 
                           placeholder="Enter product name">
                </div>

                <div class="form-group">
                    <label for="productCategory">Category *</label>
                    <select id="productCategory" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Men">Men</option>
                        <option value="Women">Women</option>
                        <option value="Kids">Kids</option>
                        <option value="Accessories">Accessories</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="productDescription">Description *</label>
                <textarea id="productDescription" name="description" rows="4" required 
                          placeholder="Enter product description"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="productPrice">Price (UGX) *</label>
                    <input type="number" id="productPrice" name="price" step="0.01" min="0" required 
                           placeholder="0.00">
                </div>

                <div class="form-group">
                    <label for="productStock">Stock Quantity</label>
                    <input type="number" id="productStock" name="stock" min="0" 
                           placeholder="0" value="0">
                </div>
            </div>

            <div class="form-group">
                <label for="productImage">Product Image * <small>(Required for new uploads, optional for updates)</small></label>
                <div class="file-upload">
                    <input type="file" id="productImage" name="image" accept="image/*" required>
                    <div class="file-preview" id="imagePreview">
                        <div id="currentImageContainer" style="display: none; text-align: center;">
                            <p><strong>Current Image:</strong></p>
                            <img id="currentImage" src="" alt="Current Product Image" 
                                 style="max-width: 200px; max-height: 200px; display: block; margin: 10px auto; border: 1px solid #ddd; border-radius: 4px;">
                            <p style="color: #666; font-size: 0.9em; margin-top: 10px;">
                                <i class="fas fa-info-circle"></i> Select a new image to change
                            </p>
                        </div>
                        <div id="uploadPrompt" style="text-align: center; padding: 30px; border: 2px dashed #ccc; border-radius: 4px; cursor: pointer;">
                            <i class="fas fa-cloud-upload-alt" style="font-size: 2.5em; color: #666; margin-bottom: 15px;"></i>
                            <p style="margin: 10px 0; color: #666; font-weight: 500;">Click to upload product image</p>
                            <p style="margin: 0; color: #999; font-size: 0.9em;">JPG, PNG, WEBP, GIF (Max: 5MB)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" id="submitBtn" class="upload-btn">
                    <i class="fas fa-upload"></i> <span id="submitText">Upload Product</span>
                </button>
                <button type="button" id="cancelEditBtn" class="cancel-btn" onclick="cancelEdit()" style="display: none;">
                    <i class="fas fa-times"></i> Cancel Edit
                </button>
            </div>
        </form>
    </div>
</section>

            <section class="products-section" id="products">
                <h2>Manage Products</h2>
                <div class="products-grid" id="adminProducts">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
                        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (empty($products)) {
                            echo '<div class="no-products">
                                    <i class="fas fa-box-open"></i>
                                    <p>No products found. Upload some products to see them here.</p>
                                  </div>';
                        } else {
                            foreach ($products as $product) {
                                $imagePath = !empty($product['image_path']) ? '../' . ltrim($product['image_path'], '/') : '../images/placeholder.jpg';
                                $formattedPrice = number_format($product['price'], 2);
                                $stockClass = $product['stock'] > 0 ? 'in-stock' : 'out-of-stock';
                                
                                echo "
                                <div class='product-card' id='product-{$product['id']}'>
                                    <div class='product-image'>
                                        <img src='{$imagePath}' alt='{$product['name']}' onerror=\"this.src='../images/placeholder.jpg'\">
                                    </div>
                                    <div class='product-info'>
                                        <h3>{$product['name']}</h3>
                                        <div class='product-meta'>
                                            <span class='product-category'>{$product['category']}</span>
                                            <span class='product-price'>UGX {$formattedPrice}</span>
                                        </div>
                                        <div class='product-stock {$stockClass}'>
                                            Stock: {$product['stock']}
                                        </div>
                                        <div class='product-actions'>
                                            <button onclick='editProduct({$product['id']})' class='btn btn-edit'>
                                                <i class='fas fa-edit'></i> Edit
                                            </button>
                                            <button onclick='deleteProduct({$product['id']})' class='btn btn-delete'>
                                                <i class='fas fa-trash'></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>";
                            }
                        }
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-error">
                                <i class="fas fa-exclamation-triangle"></i>
                                Error loading products. Please try again later.
                              </div>';
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>

  <script>
    // Image preview function
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('currentImage');
        const container = document.getElementById('currentImageContainer');
        const uploadPrompt = document.getElementById('uploadPrompt');
        
        if (file && preview && container && uploadPrompt) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                container.style.display = 'block';
                uploadPrompt.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    }
    
    // Edit product function - loads ALL data including image
    async function editProduct(id) {
        try {
            console.log('Loading product ID for editing:', id);
            
            const response = await fetch(`process/get_product.php?id=${id}`);
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                console.error('Non-JSON response:', text.substring(0, 200));
                throw new Error('Server returned non-JSON response. Check PHP errors.');
            }
            
            const product = await response.json();
            
            if (product.error) {
                throw new Error(product.error);
            }
            
            console.log('Product data loaded:', product);
            
            // Populate form fields
            document.getElementById('productId').value = product.id || '';
            document.getElementById('productName').value = product.name || '';
            document.getElementById('productDescription').value = product.description || '';
            document.getElementById('productPrice').value = product.price || '';
            document.getElementById('productCategory').value = product.category || 'Men';
            
            // Set stock if field exists
            const productStockField = document.getElementById('productStock');
            if (productStockField) {
                productStockField.value = product.stock || 0;
            }
            
            // Update form action
            document.getElementById('actionType').value = 'update';
            
            // Update UI
            document.getElementById('pageTitle').textContent = 'Edit Product';
            document.getElementById('submitText').textContent = 'Update Product';
            document.getElementById('cancelEditBtn').style.display = 'inline-block';
            
            // Store old image path
            let oldImageInput = document.getElementById('oldImagePath');
            if (!oldImageInput) {
                oldImageInput = document.createElement('input');
                oldImageInput.type = 'hidden';
                oldImageInput.id = 'oldImagePath';
                oldImageInput.name = 'old_image_path';
                document.getElementById('productForm').appendChild(oldImageInput);
            }
            oldImageInput.value = product.image_path || '';
            
            // Display current image
            const currentImage = document.getElementById('currentImage');
            const container = document.getElementById('currentImageContainer');
            const uploadPrompt = document.getElementById('uploadPrompt');
            
            if (product.image_path && currentImage && container && uploadPrompt) {
                currentImage.src = product.image_path;
                container.style.display = 'block';
                uploadPrompt.style.display = 'none';
                
                // Make image optional for updates
                document.getElementById('productImage').removeAttribute('required');
            }
            
            // Scroll to form
            document.getElementById('upload').scrollIntoView({ behavior: 'smooth' });
            
        } catch (error) {
            console.error('Error loading product:', error);
            alert('Failed to load product: ' + error.message);
        }
    }
    
    // Cancel edit function
    function cancelEdit() {
        // Reset form
        document.getElementById('productForm').reset();
        
        // Reset hidden fields
        document.getElementById('productId').value = '';
        document.getElementById('actionType').value = 'upload';
        
        // Reset UI
        document.getElementById('pageTitle').textContent = 'Upload Products';
        document.getElementById('submitText').textContent = 'Upload Product';
        document.getElementById('cancelEditBtn').style.display = 'none';
        
        // Make image required again for new uploads
        document.getElementById('productImage').required = true;
        
        // Remove old image input if exists
        const oldImageInput = document.getElementById('oldImagePath');
        if (oldImageInput) {
            oldImageInput.remove();
        }
        
        // Reset image preview
        const container = document.getElementById('currentImageContainer');
        const uploadPrompt = document.getElementById('uploadPrompt');
        const currentImage = document.getElementById('currentImage');
        
        container.style.display = 'none';
        uploadPrompt.style.display = 'block';
        currentImage.src = '';
    }
    
    // Delete product function
    async function deleteProduct(id) {
        if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            return;
        }
        
        const deleteBtn = event.target;
        const originalHTML = deleteBtn.innerHTML;
        
        try {
            // Show loading
            deleteBtn.disabled = true;
            deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            
            const response = await fetch('process/delete_product.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: id })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Remove product from DOM with animation
                const productElement = document.getElementById(`product-${id}`);
                if (productElement) {
                    productElement.style.opacity = '0';
                    productElement.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        productElement.remove();
                        
                        // Check if no products left
                        const productsGrid = document.getElementById('adminProducts');
                        if (!productsGrid.querySelector('.product-card')) {
                            productsGrid.innerHTML = `
                                <div class="no-products">
                                    <i class="fas fa-box-open"></i>
                                    <p>No products found. Upload some products to see them here.</p>
                                </div>
                            `;
                        }
                    }, 300);
                }
                
                // Show success message
                showAlert('Product deleted successfully!', 'success');
            } else {
                throw new Error(result.error || 'Failed to delete product');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        } finally {
            // Reset button
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = originalHTML;
        }
    }
    
    // Show alert message
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            ${message}
        `;
        
        const main = document.querySelector('.admin-main');
        const header = document.querySelector('.admin-header');
        main.insertBefore(alertDiv, header.nextSibling);
        
        // Remove alert after 5 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
    
    // Form submission handling
    document.getElementById('productForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate required fields
        const productName = document.getElementById('productName').value.trim();
        const productDescription = document.getElementById('productDescription').value.trim();
        const productPrice = document.getElementById('productPrice').value;
        const productCategory = document.getElementById('productCategory').value;
        const isUpdate = document.getElementById('actionType').value === 'update';
        
        if (!productName || !productDescription || !productPrice || !productCategory) {
            alert('Please fill in all required fields: Product Name, Description, Price, and Category');
            return;
        }
        
        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitBtn');
        const originalHTML = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        try {
            const response = await fetch('process/product_handler.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                if (result.action === 'update') {
                    // Update product in DOM
                    updateProductInDOM(result.product);
                    showAlert('Product updated successfully!', 'success');
                } else {
                    // Add new product to DOM
                    addProductToDOM(result.product);
                    showAlert('Product uploaded successfully!', 'success');
                }
                
                // Reset form
                cancelEdit();
            } else {
                throw new Error(result.error || 'Operation failed');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        } finally {
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        }
    });
    
    // Helper function to update product in DOM
    function updateProductInDOM(product) {
        const productElement = document.getElementById(`product-${product.id}`);
        if (productElement) {
            const imagePath = product.image_path || '../images/placeholder.jpg';
            const formattedPrice = new Intl.NumberFormat('en-US').format(product.price);
            
            productElement.querySelector('.product-image img').src = imagePath;
            productElement.querySelector('h3').textContent = product.name;
            productElement.querySelector('.product-category').textContent = product.category;
            productElement.querySelector('.product-price').textContent = `UGX ${formattedPrice}`;
            
            // Update stock if element exists
            const stockElement = productElement.querySelector('.product-stock');
            if (stockElement) {
                stockElement.textContent = `Stock: ${product.stock || 0}`;
            }
        }
    }
    
    // Helper function to add product to DOM
    function addProductToDOM(product) {
        const productsGrid = document.getElementById('adminProducts');
        const noProducts = productsGrid.querySelector('.no-products');
        
        // Remove "no products" message if present
        if (noProducts) {
            noProducts.remove();
        }
        
        const imagePath = product.image_path || '../images/placeholder.jpg';
        const formattedPrice = new Intl.NumberFormat('en-US').format(product.price);
        
        const productHTML = `
            <div class="product-card" id="product-${product.id}">
                <div class="product-image">
                    <img src="${imagePath}" alt="${product.name}" onerror="this.src='../images/placeholder.jpg'">
                </div>
                <div class="product-info">
                    <h3>${product.name}</h3>
                    <div class="product-meta">
                        <span class="product-category">${product.category}</span>
                        <span class="product-price">UGX ${formattedPrice}</span>
                    </div>
                    <div class="product-stock">
                        Stock: ${product.stock || 0}
                    </div>
                    <div class="product-actions">
                        <button onclick="editProduct(${product.id})" class="btn btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button onclick="deleteProduct(${product.id})" class="btn btn-delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Add new product at the top
        productsGrid.insertAdjacentHTML('afterbegin', productHTML);
    }
    
    // Initialize event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Image upload click handler
        const productImageInput = document.getElementById('productImage');
        const imagePreview = document.getElementById('imagePreview');
        
        if (productImageInput) {
            productImageInput.addEventListener('change', previewImage);
        }
        
        if (imagePreview) {
            imagePreview.addEventListener('click', function() {
                productImageInput.click();
            });
        }
        
        console.log('Admin dashboard initialized');
    });
</script>
</body>
</html>
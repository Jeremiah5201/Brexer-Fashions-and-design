<?php
session_start();
// Simple authentication (you should implement proper authentication)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - BREXERS FASHIONS</title>
    <link rel="stylesheet" href="../css/css/style.css">
    <link rel="stylesheet" href="../css/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                <a href="../index.html">
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
                <h1>Upload Products</h1>
            </header>

            <section class="upload-section" id="upload">
                <div class="upload-container">
                    <form action="process/upload_product.php" method="POST" enctype="multipart/form-data" class="upload-form">
                        <div class="form-group">
                            <label for="productName">Product Name</label>
                            <input type="text" id="productName" name="product_name" required>
                        </div>

                        <div class="form-group">
                            <label for="productDescription">Description</label>
                            <textarea id="productDescription" name="description" rows="4" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="productPrice">Price (UGX)</label>
                            <input type="number" id="productPrice" name="price" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label for="productCategory">Category</label>
                            <select id="productCategory" name="category">
                                <option value="Men">Men</option>
                                <option value="Women">Women</option>
                                <option value="Kids">Kids</option>
                                <option value="Accessories">Accessories</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="productImage">Product Image</label>
                            <div class="file-upload">
                                <input type="file" id="productImage" name="image" accept="image/*" required>
                                <div class="file-preview" id="imagePreview">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Click to upload product image</p>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="upload-btn">
                            <i class="fas fa-upload"></i> Upload Product
                        </button>
                    </form>
                </div>
            </section>

            <section class="products-section" id="products">
                <h2>Recent Uploads</h2>
                <div class="products-list" id="adminProducts">
                    <!-- Products will be loaded here -->
                </div>
            </section>
        </main>
    </div>

    <script>
        // Image preview
        document.getElementById('productImage').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <p>${file.name}</p>
                    `;
                }
                reader.readAsDataURL(file);
            }
        });

        // Load products for admin
        async function loadAdminProducts() {
            try {
                const response = await fetch('../process/get_products.php');
                const products = await response.json();
                
                const container = document.getElementById('adminProducts');
                container.innerHTML = '';
                
                products.forEach(product => {
                    const productItem = `
                        <div class="product-item">
                            <div class="product-image">
                                <img src="../${product.image_path}" alt="${product.name}">
                            </div>
                            <div class="product-details">
                                <h3>${product.name}</h3>
                                <p>UGX${product.price}</p>
                                <div class="product-actions">
                                    <button onclick="editProduct(${product.id})" class="delete-btn" style="background:#2ecc71;margin-right:0.5rem;">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button onclick="deleteProduct(${product.id})" class="delete-btn">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += productItem;
                });
            } catch (error) {
                console.error('Error loading products:', error);
                document.getElementById('adminProducts').innerHTML = '<p>Failed to load products.</p>';
            }
        }

        // Load products on page load
        document.addEventListener('DOMContentLoaded', loadAdminProducts);
        
        async function deleteProduct(id) {
            if (!confirm('Are you sure you want to delete this product?')) return;

            try {
                const response = await fetch('process/delete_product.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id=' + encodeURIComponent(id)
                });
                const result = await response.json();
                if (result.success) {
                    loadAdminProducts();
                } else {
                    alert(result.error || 'Failed to delete product');
                }
            } catch (e) {
                console.error(e);
                alert('An error occurred while deleting the product');
            }
        }

        function editProduct(id) {
            alert('Edit functionality can be implemented to load product data into the form for editing.');
        }
    </script>
</body>
</html>
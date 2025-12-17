<?php
// includes/db_connect.php

// Common configurations - try these in order
$configs = [
    ['host' => 'localhost', 'username' => 'root', 'password' => '', 'dbname' => 'brexers_fashion'],
    ['host' => 'localhost', 'username' => 'root', 'password' => 'root', 'dbname' => 'brexers_fashion'],
    ['host' => '127.0.0.1', 'username' => 'root', 'password' => '', 'dbname' => 'brexers_fashion'],
    ['host' => '127.0.0.1', 'username' => 'root', 'password' => 'root', 'dbname' => 'brexers_fashion'],
];

$pdo = null;
$last_error = '';

foreach ($configs as $config) {
    try {
        $pdo = new PDO(
            "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
            $config['username'],
            $config['password']
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Test connection
        $pdo->query("SELECT 1");
        
        // If we get here, connection is successful
        define('DB_CONNECTED', true);
        break;
        
    } catch (PDOException $e) {
        $last_error = $e->getMessage();
        continue;
    }
}

if (!$pdo) {
    // Fallback: Try to connect without database and create it
    try {
        $temp_pdo = new PDO("mysql:host=localhost", 'root', '');
        $temp_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $temp_pdo->exec("CREATE DATABASE IF NOT EXISTS brexers_fashion 
                       CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $temp_pdo->exec("USE brexers_fashion");
        
        // Now reconnect with database
        $pdo = new PDO(
            "mysql:host=localhost;dbname=brexers_fashion;charset=utf8mb4",
            'root',
            ''
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        define('DB_CONNECTED', true);
        
    } catch (PDOException $e) {
        // If all fails, show error
        die("<h2>Database Connection Error</h2>
             <p>Could not connect to MySQL database.</p>
             <p>Error: " . htmlspecialchars($last_error) . "</p>
             <p>Please check:</p>
             <ol>
               <li>Is MySQL running? (Start XAMPP/WAMP/MAMP)</li>
               <li>Are your database credentials correct?</li>
               <li>Does the database 'brexers_fashion' exist?</li>
             </ol>
             <p>Default credentials for local development:</p>
             <ul>
               <li>Host: localhost</li>
               <li>Username: root</li>
               <li>Password: (empty)</li>
               <li>Database: brexers_fashion</li>
             </ul>");
    }
}

// Create tables if they don't exist
function createTablesIfNotExist($pdo) {
    // Products table
    $products_sql = "
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        category VARCHAR(50) NOT NULL DEFAULT 'Men',
        price DECIMAL(10,2) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        image_path VARCHAR(500) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    try {
        $pdo->exec($products_sql);
    } catch (PDOException $e) {
        // Table might already exist with different structure
        error_log("Could not create products table: " . $e->getMessage());
    }
}

// Call the function to ensure tables exist
createTablesIfNotExist($pdo);
?>
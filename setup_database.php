<?php
// setup_database.php
echo "<h2>Database Setup for BREXERS FASHION</h2>";

// Try different common configurations
$configs = [
    ['host' => 'localhost', 'username' => 'root', 'password' => ''],
    ['host' => 'localhost', 'username' => 'root', 'password' => 'root'],
    ['host' => '127.0.0.1', 'username' => 'root', 'password' => ''],
    ['host' => '127.0.0.1', 'username' => 'root', 'password' => 'root'],
];

$connected = false;
foreach ($configs as $config) {
    try {
        $pdo = new PDO("mysql:host={$config['host']}", $config['username'], $config['password']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<p style='color: green;'>✓ Connected successfully with:</p>";
        echo "<ul>";
        echo "<li>Host: {$config['host']}</li>";
        echo "<li>Username: {$config['username']}</li>";
        echo "<li>Password: " . ($config['password'] ? 'Set' : 'Empty') . "</li>";
        echo "</ul>";
        
        // Try to create database
        try {
            $pdo->exec("CREATE DATABASE IF NOT EXISTS brexers_fashion 
                       CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "<p style='color: green;'>✓ Database 'brexers_fashion' created successfully</p>";
            
            // Create products table
            $pdo->exec("USE brexers_fashion");
            $sql = "
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
            $pdo->exec($sql);
            echo "<p style='color: green;'>✓ Products table created successfully</p>";
            
            // Create admin table
            $admin_sql = "
            CREATE TABLE IF NOT EXISTS admin_users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                last_login TIMESTAMP NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $pdo->exec($admin_sql);
            echo "<p style='color: green;'>✓ Admin users table created successfully</p>";
            
            // Insert default admin (password: admin123)
            $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT IGNORE INTO admin_users (username, password_hash, full_name, email) 
                                   VALUES (?, ?, ?, ?)");
            $stmt->execute(['admin', $hashed_password, 'Administrator', 'admin@brexersfashion.com']);
            echo "<p style='color: green;'>✓ Default admin user created (username: admin, password: admin123)</p>";
            
            $connected = true;
            break;
            
        } catch (PDOException $e) {
            echo "<p style='color: orange;'>Could not create database: " . $e->getMessage() . "</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Failed with {$config['username']}@{$config['host']}: " . $e->getMessage() . "</p>";
    }
}

if ($connected) {
    echo "<hr>";
    echo "<h3 style='color: green;'>✅ Setup Complete!</h3>";
    echo "<p>Your database has been set up successfully.</p>";
    echo "<p>Update your <code>db_connect.php</code> file with:</p>";
    echo "<pre>";
    echo "&lt;?php\n";
    echo "\$host = '{$config['host']}';\n";
    echo "\$dbname = 'brexers_fashion';\n";
    echo "\$username = '{$config['username']}';\n";
    echo "\$password = '{$config['password']}';\n";
    echo "?>";
    echo "</pre>";
} else {
    echo "<hr>";
    echo "<h3 style='color: red;'>❌ Setup Failed</h3>";
    echo "<p>Could not connect to MySQL. Please:</p>";
    echo "<ol>";
    echo "<li>Make sure MySQL is running (XAMPP/WAMP/MAMP)</li>";
    echo "<li>Check your MySQL credentials</li>";
    echo "<li>Contact your hosting provider for correct database credentials</li>";
    echo "</ol>";
}
?>
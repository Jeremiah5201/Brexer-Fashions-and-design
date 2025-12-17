<?php
// setup_infinityfree.php - Upload to InfinityFree, run, then DELETE

session_start();

if (file_exists('includes/db_connect.php') && filesize('includes/db_connect.php') > 100) {
    die("Setup already completed. Delete this file.");
}

$step = $_GET['step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? 1;
    
    if ($step == 1) {
        // Step 1: Collect database credentials
        $db_host = $_POST['db_host'];
        $db_name = $_POST['db_name'];
        $db_user = $_POST['db_user'];
        $db_pass = $_POST['db_pass'];
        $site_url = $_POST['site_url'];
        
        // Save to session
        $_SESSION['setup'] = [
            'db_host' => $db_host,
            'db_name' => $db_name,
            'db_user' => $db_user,
            'db_pass' => $db_pass,
            'site_url' => $site_url
        ];
        
        $step = 2;
    } elseif ($step == 2) {
        // Step 2: Test connection and complete setup
        $config = $_SESSION['setup'];
        
        // Test database connection
        try {
            $pdo = new PDO(
                "mysql:host={$config['db_host']}",
                $config['db_user'],
                $config['db_pass']
            );
            
            // Create database if it doesn't exist
            $pdo->exec("CREATE DATABASE IF NOT EXISTS {$config['db_name']} 
                       CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            echo "<h3 style='color: green;'>✓ Database connection successful!</h3>";
            
        } catch (PDOException $e) {
            die("<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>");
        }
        
        // Update config.php
        $config_content = file_get_contents('config.php.example');
        $config_content = str_replace([
            "'sqlXXX.epizy.com'",
            "'epiz_XXXXXX_dbname'",
            "'epiz_XXXXXX'",
            "'your_password_here'",
            "'https://yourname.epizy.com'"
        ], [
            "'" . addslashes($config['db_host']) . "'",
            "'" . addslashes($config['db_name']) . "'",
            "'" . addslashes($config['db_user']) . "'",
            "'" . addslashes($config['db_pass']) . "'",
            "'" . addslashes($config['site_url']) . "'"
        ], $config_content);
        
        file_put_contents('config.php', $config_content);
        
        // Create uploads directory
        if (!file_exists('uploads/products')) {
            mkdir('uploads/products', 0777, true);
        }
        
        // Create logs directory
        if (!file_exists('logs')) {
            mkdir('logs', 0777, true);
        }
        
        echo "<h3>Setup Complete!</h3>";
        echo "<p><a href='index.php' class='btn'>Visit Website</a></p>";
        echo "<p><a href='admin/login.php' class='btn'>Go to Admin Panel</a></p>";
        echo "<p style='color: red; font-weight: bold;'>DELETE THIS SETUP FILE NOW!</p>";
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>BREXERS FASHIONS - InfinityFree Setup</title>
    <style>
        body { font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px; }
        .step { background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px 0; }
        label { display: block; margin: 10px 0 5px; }
        input { width: 100%; padding: 8px; margin-bottom: 15px; }
        .btn { background: #3498db; color: white; padding: 10px 20px; border: none; cursor: pointer; }
        .info { background: #e3f2fd; padding: 10px; border-radius: 3px; margin: 10px 0; }
    </style>
</head>
<body>
    <h1>BREXERS FASHIONS - InfinityFree Setup</h1>
    
    <?php if ($step == 1): ?>
    <div class="step">
        <h2>Step 1: Database Configuration</h2>
        <div class="info">
            <p>Get these details from your InfinityFree Control Panel:</p>
            <ol>
                <li>Login to InfinityFree</li>
                <li>Go to "MySQL Databases"</li>
                <li>Copy the details shown there</li>
            </ol>
        </div>
        
        <form method="POST">
            <input type="hidden" name="step" value="1">
            
            <label>Database Host:</label>
            <input type="text" name="db_host" value="sqlXXX.epizy.com" required>
            
            <label>Database Name:</label>
            <input type="text" name="db_name" value="epiz_XXXXXX_dbname" required>
            
            <label>Database Username:</label>
            <input type="text" name="db_user" value="epiz_XXXXXX" required>
            
            <label>Database Password:</label>
            <input type="password" name="db_pass" required>
            
            <label>Your Website URL:</label>
            <input type="url" name="site_url" value="https://yourname.epizy.com" required>
            
            <button type="submit" class="btn">Next Step →</button>
        </form>
    </div>
    
    <?php elseif ($step == 2): ?>
    <div class="step">
        <h2>Step 2: Confirm Setup</h2>
        <p>Testing database connection and completing setup...</p>
        <?php
        // This will be handled by POST
        ?>
    </div>
    <?php endif; ?>
</body>
</html>
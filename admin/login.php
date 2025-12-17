<?php
session_start();

// Simple rate limiting per session
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Artificial delay after several failed attempts
    if ($_SESSION['login_attempts'] >= 5) {
        usleep(500000); // 0.5 second delay
    }

    // Hard-coded credentials but with password hashing
    $validUsername = 'BrexerFashion';
    $validPasswordHash = password_hash('Jerry@Brexer', PASSWORD_DEFAULT);

    if ($username === $validUsername && password_verify($password, $validPasswordHash)) {
        // Successful login: reset attempts and regenerate session ID
        $_SESSION['login_attempts'] = 0;
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit();
    } else {
        $_SESSION['login_attempts']++;
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/css/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .login-form input {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .login-btn {
            background: #667eea;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
        }
        
        .error {
            color: #ff4757;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 style="text-align: center; margin-bottom: 2rem;">Admin Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" class="login-form">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="login-btn">Login</button>
        </form>
        <p style="text-align: center; margin-top: 1rem; color: #666;">
            Enter admin dashboard
           <br> <a href="/brexers-fashion/index.php"><i class="fas fa-home"></i> Home</a>
        </p>
    </div>
</body>
</html>
<?php
require_once 'config.php';

if (isset($_POST['login'])) {
    $username = sanitize($_POST['username']);
    $password = md5($_POST['password']);
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->execute([$username, $password]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['role'] = $user['role'];
        redirect('dashboard.php');
    } else {
        $error = 'Username atau password salah!';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    redirect('index.php');
}

if (isLogin() && basename($_SERVER['PHP_SELF']) == 'index.php') {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" sizes="64x64" href="assets/logo/logoicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { 
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%); 
        }
        .login-card { 
            backdrop-filter: blur(10px); 
            background: rgba(255, 255, 255, 0.95); 
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-login {
            background: linear-gradient(135deg, #1e3a8a, #1e40af);
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #1e40af, #2563eb);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="login-card w-full max-w-md rounded-2xl shadow-2xl p-4 sm:p-8">
        <div class="text-center mb-8">
            <img src="assets/logo/logo.png" 
            alt="<?= APP_NAME ?>" 
            class="h-16 w-auto mx-auto mb-4 object-contain">
            <h2 class="text-3xl font-bold text-gray-800"><?= APP_NAME ?></h2>
            <p class="text-gray-600 mt-2">Sistem Kasir Profesional</p>
        </div>
        
        <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
            <i class="fas fa-exclamation-circle mr-2"></i><?= $error ?>
        </div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="mb-4">
                <input type="text" name="username" required 
                    placeholder="Username" 
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:outline-none">
            </div>

            <div class="mb-6">
                <input type="password" name="password" required 
                    placeholder="Password" 
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 focus:outline-none">
            </div>
            
            <button type="submit" name="login" 
                class="w-full btn-login text-white font-bold py-3 px-4 rounded-lg hover:opacity-90 transition shadow-lg">
                Log In
            </button>
        </form>
        
        <div class="text-center mt-6 text-sm text-gray-600">
            <p class="mt-4">© 2026 Julyant Marco Melandry</p>
            <p>All Rights Reserved</p>
        </div>
    </div>
</body>
</html>
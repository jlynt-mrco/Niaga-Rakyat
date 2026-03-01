<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kasir_pos');
define('BASE_URL', 'http://localhost/kasir/');
define('APP_NAME', 'Niaga Rakyat');
define('APP_VERSION', '1.0.0');

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

function isLogin() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function generateInvoice() {
    return 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
}

function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function getCurrentUser() {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'nama_lengkap' => $_SESSION['nama_lengkap'] ?? null,
        'role' => $_SESSION['role'] ?? null
    ];
}

function getCategories($pdo) {
    return $pdo->query("SELECT * FROM categories ORDER BY nama_kategori")->fetchAll();
}

function getSuppliers($pdo) {
    return $pdo->query("SELECT * FROM suppliers ORDER BY nama_supplier")->fetchAll();
}

function getProducts($pdo, $withDetails = true) {
    if ($withDetails) {
        return $pdo->query("
            SELECT p.*, 
                   COALESCE(c.nama_kategori, '-') as nama_kategori,
                   COALESCE(s.nama_supplier, '-') as nama_supplier
            FROM products p 
            LEFT JOIN categories c ON p.kategori_id = c.id 
            LEFT JOIN suppliers s ON p.supplier_id = s.id 
            ORDER BY p.id DESC
        ")->fetchAll();
    }
    return $pdo->query("SELECT * FROM products ORDER BY nama_produk")->fetchAll();
}

function uploadImage($file, $folder = 'products') {
    $target_dir = "uploads/{$folder}/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . uniqid() . '.' . $extension;
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $filename;
    }
    return '';
}
?>
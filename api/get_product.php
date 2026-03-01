<?php
// api/get_product.php - API untuk mendapatkan data produk
require_once '../config.php';

if (!isLogin()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product) {
    echo json_encode($product);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
}
?>
<?php
require_once 'config.php';
if (!isLogin()) redirect('index.php');

$invoice = $_GET['invoice'] ?? '';

$sale = $pdo->prepare("
    SELECT s.*, u.nama_lengkap 
    FROM sales s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.invoice = ?
");
$sale->execute([$invoice]);
$sale_data = $sale->fetch();

if (!$sale_data) redirect('transactions.php');

$details = $pdo->prepare("
    SELECT sd.*, p.nama_produk 
    FROM sale_details sd 
    JOIN products p ON sd.product_id = p.id 
    WHERE sd.sale_id = ?
");
$details->execute([$sale_data['id']]);
$items = $details->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" sizes="64x64" href="assets/logo/logoicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print { 
            .no-print { display: none; } 
            body { background: white; } 
            .receipt { box-shadow: none; } 
        }
        .receipt { width: 80mm; margin: 0 auto; font-family: 'Courier New', monospace; }
    </style>
</head>
<body class="bg-gray-100 p-4 sm:p-8">
    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-print mr-2"></i>Cetak Struk
        </button>
        <a href="pos.php" class="bg-green-600 text-white px-4 py-2 rounded-lg ml-2">
            <i class="fas fa-shopping-cart mr-2"></i>Kasir
        </a>
    </div>
    
    <div class="receipt bg-white shadow-lg p-4 sm:p-6">
        <div class="text-center border-b pb-3 mb-3">
            <h2 class="text-xl font-bold"><?= APP_NAME ?></h2>
            <p class="text-sm">Jl. Contoh No. 123, Jakarta</p>
            <p class="text-sm">Telp: 021-5551234</p>
        </div>
        
        <div class="text-sm mb-3">
            <p>Invoice: <?= $sale_data['invoice'] ?></p>
            <p>Tanggal: <?= date('d/m/Y H:i', strtotime($sale_data['created_at'])) ?></p>
            <p>Kasir: <?= $sale_data['nama_lengkap'] ?></p>
        </div>
        
        <div class="border-t border-b py-2 mb-3">
            <table class="w-full text-sm">
                <thead>
                    <tr>
                        <th class="text-left">Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right">Harga</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= $item['nama_produk'] ?></td>
                        <td class="text-center"><?= $item['jumlah'] ?></td>
                        <td class="text-right"><?= rupiah($item['harga_jual']) ?></td>
                        <td class="text-right"><?= rupiah($item['subtotal']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="text-sm">
            <div class="flex justify-between"><span>Total:</span><span><?= rupiah($sale_data['total_harga']) ?></span></div>
            <div class="flex justify-between"><span>Diskon:</span><span><?= rupiah($sale_data['diskon']) ?></span></div>
            <div class="flex justify-between"><span>Pajak:</span><span><?= rupiah($sale_data['pajak']) ?></span></div>
            <div class="flex justify-between font-bold text-base mt-2">
                <span>Total Bayar:</span><span><?= rupiah($sale_data['total_bayar']) ?></span>
            </div>
            <div class="flex justify-between"><span>Uang Bayar:</span><span><?= rupiah($sale_data['uang_bayar']) ?></span></div>
            <div class="flex justify-between"><span>Kembalian:</span><span><?= rupiah($sale_data['uang_kembali']) ?></span></div>
        </div>
        
        <div class="text-center text-sm mt-6 pt-3 border-t">
            <p>Terima Kasih atas Kunjungan Anda</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
        </div>
    </div>
</body>
</html>
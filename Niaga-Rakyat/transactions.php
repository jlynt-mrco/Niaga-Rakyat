<?php
require_once 'config.php';
if (!isLogin()) redirect('index.php');

$action = $_GET['action'] ?? 'list';
$id = $_GET['id'] ?? 0;

if (isset($_GET['delete']) && isAdmin()) {
    $id = (int)$_GET['delete'];
    
    try {
        $pdo->beginTransaction();
        
        $details = $pdo->prepare("SELECT product_id, jumlah FROM sale_details WHERE sale_id = ?");
        $details->execute([$id]);
        $detail_data = $details->fetchAll();
        
        foreach ($detail_data as $d) {
            $stmt = $pdo->prepare("UPDATE products SET stok = stok + ? WHERE id = ?");
            $stmt->execute([$d['jumlah'], $d['product_id']]);
        }
        
        $stmt = $pdo->prepare("DELETE FROM sales WHERE id = ?");
        $stmt->execute([$id]);
        
        $pdo->commit();
        $_SESSION['success'] = 'Transaksi berhasil dihapus';
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Gagal menghapus transaksi: ' . $e->getMessage();
    }
    
    header('Location: transactions.php');
    exit;
}

if ($action == 'detail' && $id) {
    $transaction = $pdo->prepare("
        SELECT s.*, u.nama_lengkap 
        FROM sales s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.id = ?
    ");
    $transaction->execute([$id]);
    $transaction = $transaction->fetch();
    
    if (!$transaction) {
        redirect('transactions.php');
    }
    
    $details = $pdo->prepare("
        SELECT sd.*, p.nama_produk, p.harga_beli 
        FROM sale_details sd 
        JOIN products p ON sd.product_id = p.id 
        WHERE sd.sale_id = ?
    ");
    $details->execute([$id]);
    $items = $details->fetchAll();
    
    $total_laba = 0;
    foreach ($items as $item) {
        $total_laba += $item['subtotal'] - ($item['harga_beli'] * $item['jumlah']);
    }
} else {
    $transactions = $pdo->query("
        SELECT s.*, u.nama_lengkap,
               (SELECT COUNT(*) FROM sale_details WHERE sale_id = s.id) as total_item
        FROM sales s 
        JOIN users u ON s.user_id = u.id 
        ORDER BY s.created_at DESC
    ")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" sizes="64x64" href="assets/logo/logoicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $action == 'detail' ? 'Detail Transaksi' : 'Daftar Transaksi' ?> - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.tailwind.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50">

<?php include 'sidebar.php'; ?>

<div class="p-4 sm:p-8">
    <?php if ($action == 'detail'): ?>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4 sm:mb-0">Detail Transaksi</h1>
            <div class="flex gap-2">
                <a href="transactions.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-sm">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
                <a href="receipt.php?invoice=<?= $transaction['invoice'] ?>" class="bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 text-sm">
                    <i class="fas fa-print mr-2"></i>Cetak Struk
                </a>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Invoice</p>
                <p class="text-xl font-bold"><?= $transaction['invoice'] ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Tanggal</p>
                <p class="text-xl font-bold"><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></p>
            </div>
            <div class="bg-white rounded-xl shadow-lg p-6">
                <p class="text-gray-500 text-sm">Kasir</p>
                <p class="text-xl font-bold"><?= $transaction['nama_lengkap'] ?></p>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 overflow-x-auto">
            <h2 class="text-xl font-bold mb-4">Item yang Dibeli</h2>
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left">Produk</th>
                        <th class="px-4 py-3 text-right">Harga</th>
                        <th class="px-4 py-3 text-center">Jumlah</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr class="border-b">
                        <td class="px-4 py-3"><?= $item['nama_produk'] ?></td>
                        <td class="px-4 py-3 text-right"><?= rupiah($item['harga_jual']) ?></td>
                        <td class="px-4 py-3 text-center"><?= $item['jumlah'] ?></td>
                        <td class="px-4 py-3 text-right"><?= rupiah($item['subtotal']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Ringkasan Pembayaran</h2>
                <div class="space-y-2">
                    <div class="flex justify-between"><span>Total Belanja:</span><span><?= rupiah($transaction['total_harga']) ?></span></div>
                    <div class="flex justify-between"><span>Diskon:</span><span class="text-red-600">- <?= rupiah($transaction['diskon']) ?></span></div>
                    <div class="flex justify-between"><span>Pajak:</span><span><?= rupiah($transaction['pajak']) ?></span></div>
                    <div class="flex justify-between text-lg font-bold pt-2 border-t">
                        <span>Total Bayar:</span><span class="text-blue-600"><?= rupiah($transaction['total_bayar']) ?></span>
                    </div>
                    <div class="flex justify-between"><span>Uang Bayar:</span><span><?= rupiah($transaction['uang_bayar']) ?></span></div>
                    <div class="flex justify-between"><span>Kembalian:</span><span class="text-green-600"><?= rupiah($transaction['uang_kembali']) ?></span></div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-bold mb-4">Ringkasan Laba</h2>
                <div class="space-y-2">
                    <div class="flex justify-between"><span>Total Laba Kotor:</span><span class="text-green-600 font-bold"><?= rupiah($total_laba) ?></span></div>
                    <div class="flex justify-between">
                        <span>Margin Laba:</span>
                        <span><?= $transaction['total_harga'] > 0 ? number_format(($total_laba / $transaction['total_harga']) * 100, 1) : 0 ?>%</span>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-4 sm:mb-0">Daftar Transaksi</h1>
            <a href="pos.php" class="bg-blue-800 text-white px-4 py-2 rounded-lg hover:bg-blue-900 text-sm">
                <i class="fas fa-plus mr-2"></i>Transaksi Baru
            </a>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
            <form method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                <div class="w-full sm:w-auto">
                    <label class="block text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="<?= $start_date ?? date('Y-m-01') ?>" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div class="w-full sm:w-auto">
                    <label class="block text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="<?= $end_date ?? date('Y-m-d') ?>" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div class="w-full sm:w-auto">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 overflow-x-auto">
            <?php if (empty($transactions)): ?>
                <p class="text-center text-gray-500 py-8">Belum ada transaksi</p>
            <?php else: ?>
            <table id="transactionsTable" class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-3 text-left">Invoice</th>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Kasir</th>
                        <th class="px-4 py-3 text-center">Item</th>
                        <th class="px-4 py-3 text-right">Total</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $t): ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium"><?= $t['invoice'] ?></td>
                        <td class="px-4 py-3"><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                        <td class="px-4 py-3"><?= $t['nama_lengkap'] ?></td>
                        <td class="px-4 py-3 text-center"><?= $t['total_item'] ?></td>
                        <td class="px-4 py-3 text-right font-bold text-blue-600"><?= rupiah($t['total_bayar']) ?></td>
                        <td class="px-4 py-3 text-center">
                            <a href="transactions.php?action=detail&id=<?= $t['id'] ?>" class="text-blue-600 hover:text-blue-800 mx-1" title="Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="receipt.php?invoice=<?= $t['invoice'] ?>" class="text-green-600 hover:text-green-800 mx-1" title="Cetak Struk">
                                <i class="fas fa-print"></i>
                            </a>
                            <?php if (isAdmin()): ?>
                            <a href="?delete=<?= $t['id'] ?>" onclick="return confirm('Yakin ingin menghapus transaksi ini?\nStok produk akan dikembalikan.')" class="text-red-600 hover:text-red-800 mx-1" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.tailwind.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
    $(document).ready(function() {
        if ($('#transactionsTable').length) {
            $('#transactionsTable').DataTable({ 
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' },
                order: [[1, 'desc']]
            });
        }
    });
</script>
</body>
</html>
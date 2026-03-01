<?php
require_once 'config.php';
if (!isLogin()) redirect('index.php');

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$sales_report = $pdo->prepare("
    SELECT s.*, u.nama_lengkap 
    FROM sales s 
    JOIN users u ON s.user_id = u.id 
    WHERE DATE(s.created_at) BETWEEN ? AND ?
    ORDER BY s.created_at DESC
");
$sales_report->execute([$start_date, $end_date]);
$sales = $sales_report->fetchAll();

$summary = $pdo->prepare("
    SELECT 
        COUNT(*) as total_transaksi,
        COALESCE(SUM(total_bayar), 0) as total_pendapatan,
        COALESCE(SUM(diskon), 0) as total_diskon,
        COALESCE(SUM(pajak), 0) as total_pajak,
        COALESCE(SUM(total_bayar - (total_harga - diskon)), 0) as total_laba
    FROM sales 
    WHERE DATE(created_at) BETWEEN ? AND ?
");
$summary->execute([$start_date, $end_date]);
$summary_data = $summary->fetch();

$best_selling = $pdo->prepare("
    SELECT p.nama_produk, SUM(sd.jumlah) as total_terjual, SUM(sd.subtotal) as total_penjualan
    FROM sale_details sd
    JOIN products p ON sd.product_id = p.id
    JOIN sales s ON sd.sale_id = s.id
    WHERE DATE(s.created_at) BETWEEN ? AND ?
    GROUP BY p.id
    ORDER BY total_terjual DESC
    LIMIT 10
");
$best_selling->execute([$start_date, $end_date]);
$best_products = $best_selling->fetchAll();

if (isset($_GET['export_csv'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="Laporan_Penjualan_' . $start_date . '_' . $end_date . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, ['Invoice', 'Tanggal', 'Kasir', 'Total Belanja', 'Diskon', 'Pajak', 'Total Bayar']);
    
    foreach ($sales as $s) {
        fputcsv($output, [
            $s['invoice'],
            date('d/m/Y H:i', strtotime($s['created_at'])),
            $s['nama_lengkap'],
            $s['total_harga'],
            $s['diskon'],
            $s['pajak'],
            $s['total_bayar']
        ]);
    }
    
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" sizes="64x64" href="assets/logo/logoicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.tailwind.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50">

<?php include 'sidebar.php'; ?>

<div class="p-4 sm:p-8">
    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-8">Laporan Penjualan</h1>
    
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full sm:w-auto">
                <label class="block text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" name="start_date" value="<?= $start_date ?>" class="w-full border rounded-lg px-4 py-2">
            </div>
            <div class="w-full sm:w-auto">
                <label class="block text-gray-700 mb-2">Tanggal Akhir</label>
                <input type="date" name="end_date" value="<?= $end_date ?>" class="w-full border rounded-lg px-4 py-2">
            </div>
            <div class="w-full sm:w-auto flex gap-2">
                <button type="submit" class="bg-blue-800 text-white px-6 py-2 rounded-lg hover:bg-blue-900">
                    <i class="fas fa-search mr-2"></i>Tampilkan
                </button>
                <a href="?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>&export_csv=1" 
                   class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </a>
            </div>
        </form>
    </div>
    
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-500 text-xs">Transaksi</p>
            <p class="text-lg font-bold"><?= $summary_data['total_transaksi'] ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-500 text-xs">Pendapatan</p>
            <p class="text-lg font-bold text-green-600 truncate"><?= rupiah($summary_data['total_pendapatan']) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-500 text-xs">Diskon</p>
            <p class="text-lg font-bold text-red-600"><?= rupiah($summary_data['total_diskon']) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-500 text-xs">Pajak</p>
            <p class="text-lg font-bold text-purple-600"><?= rupiah($summary_data['total_pajak']) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-4">
            <p class="text-gray-500 text-xs">Laba</p>
            <p class="text-lg font-bold text-blue-800"><?= rupiah($summary_data['total_laba']) ?></p>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 overflow-x-auto">
        <h2 class="text-xl font-bold mb-4">Daftar Transaksi</h2>
        <?php if (empty($sales)): ?>
            <p class="text-center text-gray-500 py-8">Belum ada transaksi di periode ini</p>
        <?php else: ?>
        <table id="salesTable" class="min-w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-3 text-left">Invoice</th>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Kasir</th>
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-right">Diskon</th>
                    <th class="px-4 py-3 text-right">Pajak</th>
                    <th class="px-4 py-3 text-right">Total Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $s): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3"><?= $s['invoice'] ?></td>
                    <td class="px-4 py-3"><?= date('d/m/Y H:i', strtotime($s['created_at'])) ?></td>
                    <td class="px-4 py-3"><?= $s['nama_lengkap'] ?></td>
                    <td class="px-4 py-3 text-right"><?= rupiah($s['total_harga']) ?></td>
                    <td class="px-4 py-3 text-right"><?= rupiah($s['diskon']) ?></td>
                    <td class="px-4 py-3 text-right"><?= rupiah($s['pajak']) ?></td>
                    <td class="px-4 py-3 text-right font-bold text-blue-600"><?= rupiah($s['total_bayar']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 overflow-x-auto">
        <h2 class="text-xl font-bold mb-4">Produk Terlaris</h2>
        <?php if (empty($best_products)): ?>
            <p class="text-center text-gray-500 py-4">Belum ada data penjualan</p>
        <?php else: ?>
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-3 text-left">Produk</th>
                    <th class="px-4 py-3 text-center">Total Terjual</th>
                    <th class="px-4 py-3 text-right">Total Penjualan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($best_products as $bp): ?>
                <tr class="border-b">
                    <td class="px-4 py-3"><?= $bp['nama_produk'] ?></td>
                    <td class="px-4 py-3 text-center"><?= $bp['total_terjual'] ?></td>
                    <td class="px-4 py-3 text-right"><?= rupiah($bp['total_penjualan']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.tailwind.min.js"></script>
<script src="assets/js/script.js"></script>
<script>
    $(document).ready(function() {
        if ($('#salesTable').length) {
            $('#salesTable').DataTable({
                language: { url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json' },
                order: [[1, 'desc']],
                pageLength: 25
            });
        }
    });
</script>
</body>
</html>
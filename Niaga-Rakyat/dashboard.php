<?php
require_once 'config.php';
if (!isLogin()) redirect('index.php');

$stats = [
    'total_produk' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'total_transaksi' => $pdo->query("SELECT COUNT(*) FROM sales WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
    'total_pendapatan' => $pdo->query("SELECT COALESCE(SUM(total_bayar), 0) FROM sales WHERE DATE(created_at) = CURDATE()")->fetchColumn(),
    'total_laba' => $pdo->query("
        SELECT COALESCE(SUM(sd.subtotal - (p.harga_beli * sd.jumlah)), 0) 
        FROM sale_details sd 
        JOIN products p ON sd.product_id = p.id 
        JOIN sales s ON sd.sale_id = s.id 
        WHERE DATE(s.created_at) = CURDATE()
    ")->fetchColumn()
];

$chart_data = ['labels' => [], 'data' => []];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $total = $pdo->prepare("SELECT COALESCE(SUM(total_bayar), 0) FROM sales WHERE DATE(created_at) = ?");
    $total->execute([$date]);
    $chart_data['labels'][] = date('d M', strtotime($date));
    $chart_data['data'][] = $total->fetchColumn();
}

$recent_transactions = $pdo->query("
    SELECT s.*, u.nama_lengkap 
    FROM sales s 
    JOIN users u ON s.user_id = u.id 
    WHERE DATE(s.created_at) = CURDATE() 
    ORDER BY s.created_at DESC LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/png" sizes="64x64" href="assets/logo/logoicon.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= APP_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-50">

<?php include 'sidebar.php'; ?>

<div class="p-4 sm:p-8">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2 sm:mb-0">Dashboard</h1>
        <div class="text-sm text-gray-600">
            <i class="far fa-calendar-alt mr-2"></i><?= date('d F Y') ?>
        </div>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-600">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-boxes text-2xl text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Produk</p>
                    <p class="text-2xl font-bold"><?= $stats['total_produk'] ?></p>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-600">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-shopping-cart text-2xl text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Transaksi Hari Ini</p>
                    <p class="text-2xl font-bold"><?= $stats['total_transaksi'] ?></p>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-600">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-money-bill-wave text-2xl text-purple-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Pendapatan Hari Ini</p>
                    <p class="text-2xl font-bold"><?= rupiah($stats['total_pendapatan']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="stat-card bg-white rounded-xl shadow-lg p-6 border-l-4 border-yellow-600">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-chart-line text-2xl text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Laba Hari Ini</p>
                    <p class="text-2xl font-bold"><?= rupiah($stats['total_laba']) ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Grafik Penjualan 7 Hari Terakhir</h2>
            <canvas id="salesChart" height="100"></canvas>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">Transaksi Hari Ini</h2>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                <?php if (empty($recent_transactions)): ?>
                    <p class="text-gray-500 text-center py-4">Belum ada transaksi hari ini</p>
                <?php else: ?>
                    <?php foreach ($recent_transactions as $t): ?>
                    <a href="transactions.php?action=detail&id=<?= $t['id'] ?>" class="block p-3 border rounded-lg hover:bg-blue-50 transition">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                            <div class="mb-2 sm:mb-0">
                                <p class="font-semibold"><?= $t['invoice'] ?></p>
                                <p class="text-sm text-gray-600"><?= date('H:i', strtotime($t['created_at'])) ?></p>
                            </div>
                            <div class="text-left sm:text-right">
                                <p class="font-bold text-blue-600"><?= rupiah($t['total_bayar']) ?></p>
                                <p class="text-xs text-gray-500"><?= $t['nama_lengkap'] ?></p>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                    <a href="transactions.php" class="block text-center text-blue-600 text-sm mt-2 hover:underline">
                        Lihat semua transaksi →
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

</div>

<script src="assets/js/script.js"></script>
<script>
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($chart_data['labels']) ?>,
            datasets: [{
                label: 'Total Penjualan',
                data: <?= json_encode($chart_data['data']) ?>,
                borderColor: 'rgb(37, 99, 235)',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } }
        }
    });
</script>
</body>
</html>
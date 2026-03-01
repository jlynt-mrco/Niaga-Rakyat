<?php
$current_page = basename($_SERVER['PHP_SELF']);
$user = getCurrentUser();
?>

<button id="mobileMenuToggle" class="fixed top-4 right-4 z-50 md:hidden bg-blue-600 text-white p-3 rounded-lg shadow-lg">
    <i class="fas fa-bars text-xl"></i>
</button>

<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden"></div>

<div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-gradient-to-b from-blue-900 to-blue-950 text-white shadow-xl z-50 -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    
    <div class="p-4 border-b border-blue-800 flex justify-between items-center">
        <h2 class="text-base sm:text-lg font-bold flex items-end max-w-[calc(100%-40px)]">
            <img src="assets/logo/logosidebar.png" alt="<?= APP_NAME ?>" class="h-7 w-auto mr-2 flex-shrink-0">
            <span class="truncate"><?= APP_NAME ?></span>
            <span class="text-[10px] font-normal text-blue-300 ml-1">v.0.1</span>
        </h2>
        <button id="closeSidebarBtn" class="md:hidden text-white hover:text-gray-300 text-2xl flex-shrink-0">
            <i class="fas fa-times"></i>
        </button>
    </div>
        
    <div class="px-6 py-2 text-xs text-blue-300 border-b border-blue-800">
        © 2026 Julyant Marco Melandry
    </div>

    <nav class="mt-6 px-4">
        <a href="dashboard.php" class="flex items-center px-4 py-3 mb-2 rounded-lg transition <?= ($current_page == 'dashboard.php') ? 'bg-blue-700' : 'hover:bg-blue-800' ?>">
            <i class="fas fa-home w-6"></i>
            <span class="ml-3">Dashboard</span>
        </a>
        
        <a href="products.php" class="flex items-center px-4 py-3 mb-2 rounded-lg transition <?= ($current_page == 'products.php') ? 'bg-blue-700' : 'hover:bg-blue-800' ?>">
            <i class="fas fa-box w-6"></i>
            <span class="ml-3">Produk</span>
        </a>
        
        <a href="pos.php" class="flex items-center px-4 py-3 mb-2 rounded-lg transition <?= ($current_page == 'pos.php') ? 'bg-blue-700' : 'hover:bg-blue-800' ?>">
            <i class="fas fa-shopping-cart w-6"></i>
            <span class="ml-3">Kasir</span>
        </a>
        
        <a href="transactions.php" class="flex items-center px-4 py-3 mb-2 rounded-lg transition <?= ($current_page == 'transactions.php') ? 'bg-blue-700' : 'hover:bg-blue-800' ?>">
            <i class="fas fa-history w-6"></i>
            <span class="ml-3">Transaksi</span>
        </a>
        
        <a href="reports.php" class="flex items-center px-4 py-3 mb-2 rounded-lg transition <?= ($current_page == 'reports.php') ? 'bg-blue-700' : 'hover:bg-blue-800' ?>">
            <i class="fas fa-chart-bar w-6"></i>
            <span class="ml-3">Laporan</span>
        </a>
    </nav>
    
    <div class="absolute bottom-0 w-full p-6 border-t border-blue-800">
        <div class="flex items-center">
            <div class="bg-blue-700 rounded-full p-3">
                <i class="fas fa-user"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="font-medium truncate"><?= $user['nama_lengkap'] ?? 'User' ?></p>
                <p class="text-xs text-blue-300"><?= $user['role'] ?? 'Guest' ?></p>
            </div>
            <a href="index.php?logout=1" class="text-blue-300 hover:text-white">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</div>

<div class="md:ml-64 min-h-screen bg-gray-50 transition-all duration-300">